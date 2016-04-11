<?php

namespace Doornock\ApiModule\Presenters;

use Doornock\ApiModule\Model\AuthenticationException;
use Doornock\ApiModule\Model\ApiAuthenticator;
use Doornock\Model\DoorModule\DeviceAccessManager;
use Doornock\Model\DoorModule\DeviceRepository;
use Doornock\Model\DoorModule\Door;
use Nette;


class DoorPresenter extends BasePresenter
{

	/** @var DeviceAccessManager */
	private $deviceManager;


	/** @var ApiAuthenticator */
	private $deviceAuthenticator;

	/**
	 * DoorPresenter constructor.
	 * @param DeviceAccessManager $deviceManager
	 * @param ApiAuthenticator $apiAuthenticator
	 */
	public function __construct(
		DeviceAccessManager $deviceManager,
		ApiAuthenticator $apiAuthenticator
	)
	{
		parent::__construct();
		$this->deviceManager = $deviceManager;
		$this->deviceAuthenticator = $apiAuthenticator;
	}


	public function actionList()
	{
		try {
			$device = $this->deviceAuthenticator->authenticateDevice($this->getHttpRequest());

			if ($device->isBlocked()) {
				$this->sendRequestError(403, "Device is blocked", 10, NULL, $device->getApiKey());
			}

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

			$this->sendSuccess($return, $device->getApiKey());
		} catch (AuthenticationException $e) {
			$this->sendRequestError(401, "Authentication failed", $e->getCode());
		}
	}


	public function actionOpen()
	{
		try {
			$params = $this->getRequestPostParams();
			$device = $this->deviceAuthenticator->authenticateDevice($this->getHttpRequest());

			if ($device->isBlocked()) {
				$this->sendRequestError(403, "Device is blocked", 10, NULL, $device->getApiKey());
			}


			if ($this->deviceManager->openDoor($device, $params->requireParamString('door_id'))) {
				$this->sendSuccess([], $device->getApiKey());
			} else {
				$this->sendRequestError(400, "Door is not working or not found you have no access");
			}
		} catch (AuthenticationException $e) {
			$this->sendRequestError(401, "Authentication failed", $e->getCode());
		}
	}

}
