<?php

namespace Doornock\ApiModule\Presenters;

use Doornock\Model\DoorModule\ApiKeyNotFoundException;
use Doornock\Model\DoorModule\DeviceAccessManager;
use Doornock\Model\DoorModule\DeviceIsBlockedException;
use Doornock\Model\DoorModule\Door;
use Nette;


class DoorPresenter extends BasePresenter
{

	/** @var DeviceAccessManager */
	private $deviceManager;

	/**
	 * DoorPresenter constructor.
	 * @param DeviceAccessManager $deviceManager
	 */
	public function __construct(DeviceAccessManager $deviceManager)
	{
		parent::__construct();
		$this->deviceManager = $deviceManager;
	}


	public function actionList($api_key)
	{
		try {
			$doors = $this->deviceManager->findDoorWithAccess($api_key);

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
		} catch (DeviceIsBlockedException $e) {
			$this->sendRequestError(403, "Device is blocked");
		} catch (ApiKeyNotFoundException $e) {
			$this->sendRequestError(401, "Api key not found");
		}
	}


	public function actionOpen($api_key, $door_id)
	{
		try {
			if ($this->deviceManager->openDoor($api_key, $door_id)) {
				$this->sendSuccess();
			} else {
				$this->sendRequestError(400, "Door is not working or not found you have no access");
			}
		} catch (DeviceIsBlockedException $e) {
			$this->sendRequestError(403, "Device is blocked");
		} catch (ApiKeyNotFoundException $e) {
			$this->sendRequestError(401, "Api key not found");
		}
	}

}
