<?php

namespace Doornock\AdminModule\Presenters;


use Doornock\AdminModule\Forms\AddUserFormFactory;
use Doornock\AdminModule\Forms\ChangePasswordFormFactory;
use Doornock\AdminModule\Forms\ChangeRoleFormFactory;
use Doornock\Model\UserModule\User;
use Doornock\Model\UserModule\UserManager;
use Doornock\Model\UserModule\UserRepository;
use Nette\Http\IResponse;

class UserDetailPresenter extends BasePresenter
{
	/** @var AddUserFormFactory @inject */
	public $addUserFormFactory;


	/** @var ChangePasswordFormFactory @inject */
	public $changePasswordFormFactory;


	/** @var ChangeRoleFormFactory @inject */
	public $changeRoleFormFactory;


	/** @var UserRepository */
	public $userRepository;


	/** @var UserManager */
	public $userManager;


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


	public function __construct(UserRepository $userRepository, UserManager $userManager)
	{
		parent::__construct();
		$this->userRepository = $userRepository;
		$this->userManager = $userManager;
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
	}


	public function renderDefault()
	{
		$this->template->selectedUser = $this->selectedUser;
		$this->template->isYou = $this->selectedUser->getId() === $this->user->getId();
		$this->template->allowChangeRole = $this->user->isAllowed('admin_users', 'change_role');
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
}
