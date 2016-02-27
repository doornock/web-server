<?php

namespace Doornock\AdminModule\Presenters;


use Doornock\AdminModule\Components\DeviceGridFactory;
use Doornock\AdminModule\Components\DoorAccessGridFactory;
use Doornock\AdminModule\Components\DoorWithAccessGridFactory;
use Doornock\AdminModule\Forms\AddUserFormFactory;
use Doornock\AdminModule\Forms\ChangePasswordFormFactory;
use Doornock\AdminModule\Forms\ChangeRoleFormFactory;
use Doornock\Model\DoorModule\AccessManager;
use Doornock\Model\DoorModule\AccessUnauthorizedException;
use Doornock\Model\DoorModule\DeviceManager;
use Doornock\Model\DoorModule\DeviceNotFoundException;
use Doornock\Model\DoorModule\Door;
use Doornock\Model\DoorModule\DoorRepository;
use Doornock\Model\DoorModule\NodeExecuteCommandException;
use Doornock\Model\DoorModule\WebAccessManager;
use Doornock\Model\UserModule\User;
use Doornock\Model\UserModule\UserManager;
use Doornock\Model\UserModule\UserRepository;
use Nette\Http\IResponse;
use Nextras\Application\UI\SecuredLinksPresenterTrait;
use Tracy\Debugger;

class UserDetailPresenter extends BasePresenter
{

	use SecuredLinksPresenterTrait;

	/** @var AddUserFormFactory @inject */
	public $addUserFormFactory;


	/** @var ChangePasswordFormFactory @inject */
	public $changePasswordFormFactory;


	/** @var ChangeRoleFormFactory @inject */
	public $changeRoleFormFactory;


	/** @var DeviceGridFactory @inject */
	public $deviceGridFactory;


	/** @var DoorWithAccessGridFactory @inject */
	public $doorWithAccessGridFactory;

	/** @var DoorAccessGridFactory @inject */
	public $doorAccessGridFactory;

	/** @var UserRepository */
	public $userRepository;


	/** @var UserManager @inject */
	public $userManager;


	/** @var DeviceManager @inject */
	public $deviceManager;


	/** @var AccessManager @inject */
	public $accessManager;

	/** @var WebAccessManager @inject */
	public $webAccessManager;


	/** @var DoorRepository */
	private $doorRepository;


	/**
	 * @persistent
	 * @var int
	 */
	public $userId;


	/**
	 * Loaded entity of selected user (or logged user)
	 * @var User
	 */
	private $selectedUser;


	/** @var bool */
	private $allowChangeAccess;


	public function __construct(UserRepository $userRepository, DoorRepository $doorRepository)
	{
		parent::__construct();
		$this->userRepository = $userRepository;
		$this->doorRepository = $doorRepository;
	}


	protected function startup()
	{
		parent::startup();

		if ($this->userId === NULL) {
			$this->selectedUser = $this->user->getIdentity();
		} else {
			$this->selectedUser = $this->userRepository->getById($this->userId);
			if (!$this->user->isAllowed('admin_users')) {
				$this->error('No access', IResponse::S403_FORBIDDEN);
			}
		}

		if (!$this->selectedUser) {
			$this->flashMessage('User not found!', 'danger');
			$this->redirect('default');
		}

		$this->allowChangeAccess = $this->user->isAllowed('admin_nodes', 'change_access');
	}


	/**
	 * @secured
	 */
	public function handleAllowAccess($doorId)
	{
		if (!$this->allowChangeAccess) {
			$this->error('No access', IResponse::S403_FORBIDDEN);
		}
		$door = $this->doorRepository->find($doorId); /** @var $door Door */
		if (!$door) {
			$this->flashMessage('Door not found', 'danger');
			$this->redirect('this');
		}

		$this->accessManager->allow($this->selectedUser, $door);
		$this->redirect('this');
	}


	/**
	 * @secured
	 */
	public function handleDenyAccess($doorId)
	{
		if (!$this->allowChangeAccess) {
			$this->error('No access', IResponse::S403_FORBIDDEN);
		}

		$door = $this->doorRepository->find($doorId);  /** @var $door Door */
		if (!$door) {
			$this->flashMessage('Door not found', 'danger');
			$this->redirect('this');
		}

		$this->accessManager->deny($this->selectedUser, $door);
		$this->redirect('this');
	}


	/**
	 * @secured
	 */
	public function handleBlockDevice($deviceId)
	{
		try {
			if ($this->user->isAllowed('admin_users', 'block_every_device')) {
				$this->deviceManager->blockDeviceById($deviceId);
			} else {
				$this->deviceManager->blockDeviceById($deviceId, $this->user->getIdentity());
			}
		} catch (DeviceNotFoundException $e) {
			$this->flashMessage('Device not found', 'danger');
		}
	}


	/**
	 * @secured
	 */
	public function handleOpenDoor($doorId)
	{
		try {
			if ($this->webAccessManager->openDoor($doorId)) {
				$this->flashMessage('Door was successfully opened', 'success');
			} else {
				$this->flashMessage('No access, or door not found', 'warning');
			}
		} catch (AccessUnauthorizedException $e) {
			$this->flashMessage('No access', 'danger');
		} catch (NodeExecuteCommandException $e) {
			if (Debugger::$productionMode) {
				Debugger::log($e);
			} else {
				throw $e;
			}
			$this->flashMessage('Door terminal (node) is not working, need technical support', 'danger');
		}
		$this->redirect('this');
	}



	public function renderDefault()
	{
		$this->template->selectedUser = $this->selectedUser;
		$this->template->isYou = $isYou = $this->selectedUser->getId() === $this->user->getId();
		$this->template->allowChangeRole = !$isYou && $this->user->isAllowed('admin_users', 'change_role');
		$this->template->allowChangeAccess = $this->allowChangeAccess;
	}


	public function createComponentChangePasswordForm()
	{
		$this->changePasswordFormFactory->setUser($this->selectedUser);

		if ($this->user->isAllowed('admin_users', 'change_password_without_actual')) {
			$this->changePasswordFormFactory->disableCheckActualPassword();
		}

		$component = $this->changePasswordFormFactory->create();
		$component->onSuccess[] = function () {
			$this->flashMessage("User's password have successfully changed", 'success');
			$this->redirect('this');
		};
		return $component;
	}


	public function createComponentRoleForm()
	{
		$this->changeRoleFormFactory->setUser($this->selectedUser);
		$component = $this->changeRoleFormFactory->create();
		$component->onSuccess[] = function () {
			$this->flashMessage("Role have successfully changed", 'success');
			$this->redirect('this');
		};
		return $component;
	}


	public function createComponentDeviceGrid()
	{
		$grid = $this->deviceGridFactory->create($this->selectedUser);
		$grid->addCellsTemplate(__DIR__ . '/templates/BaseGrid.latte');
		$grid->addCellsTemplate(__DIR__ . '/templates/UserDetail/DeviceGrid.latte');
		return $grid;
	}


	public function createComponentDoorWithAccessGrid()
	{
		$grid = $this->doorWithAccessGridFactory->create($this->selectedUser);
		$grid->addCellsTemplate(__DIR__ . '/templates/BaseGrid.latte');
		$grid->addCellsTemplate(__DIR__ . '/templates/UserDetail/WithAccessGrid.latte');
		return $grid;
	}


	public function createComponentDoorAccessGrid()
	{
		$grid = $this->doorAccessGridFactory->create($this->selectedUser);
		$grid->addCellsTemplate(__DIR__ . '/templates/BaseGrid.latte');
		$grid->addCellsTemplate(__DIR__ . '/templates/UserDetail/AccessGrid.latte');
		return $grid;
	}
}
