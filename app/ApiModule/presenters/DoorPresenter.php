<?php

namespace Doornock\ApiModule\Presenters;

use Doornock\ApiModule\Model\AuthenticationException;
use Doornock\ApiModule\Model\DeviceAuthenticator;
use Doornock\Model\DoorModule\DeviceAccessFasade;
use Doornock\Model\DoorModule\DeviceRepository;
use Doornock\Model\DoorModule\Door;
use Nette;


class DoorPresenter extends BasePresenter
{

	/** @var DeviceAccessFasade */
	private $deviceManager;


	/** @var DeviceAuthenticator */
	private $deviceAuthenticator;

	/**
	 * DoorPresenter constructor.
	 * @param DeviceAccessFasade $deviceManager
	 * @param DeviceRepository $deviceRepository
	 */
	public function __construct(DeviceAccessFasade $deviceManager, DeviceRepository $deviceRepository)
	{
		parent::__construct();
		$this->deviceManager = $deviceManager;
		$this->deviceAuthenticator = new DeviceAuthenticator($deviceRepository);
	}


	public function actionList()
	{
		try {
			$device = $this->deviceAuthenticator->authenticate($this->getHttpRequest());
			$doors = $this->deviceManager->findDoorWithAccess($device);

			$return = array();
			foreach ($doors as $door) {
				/** @var Door $door */
				$obj = new \stdClass();
				$obj->id = $door->getId();
				$obj->title = $door->getTitle();
				$obj->access = TRUE; // @todo maybe remove?
				$return[] = $obj;
			}

			$this->sendSuccess($return);
		} catch (AuthenticationException $e) {
			$this->sendRequestError($e->isAuthorizationProblem() ? 403 : 401, "Authentication failed", $e->getCode());
		}
	}


	public function actionOpen()
	{
		try {
			$params = $this->getRequestPostParams();
			if (!isset($params['door_id'])) {
				$this->sendRequestError(400, 'Missing door_id parameter');
			}
			$device = $this->deviceAuthenticator->authenticate($this->getHttpRequest());
			if ($this->deviceManager->openDoor($device, $params['door_id'])) {
				$this->sendSuccess();
			} else {
				$this->sendRequestError(400, "Door is not working or not found you have no access");
			}
		} catch (AuthenticationException $e) {
			$this->sendRequestError($e->isAuthorizationProblem() ? 403 : 401, "Authentication failed", $e->getCode());
		}
	}

}
