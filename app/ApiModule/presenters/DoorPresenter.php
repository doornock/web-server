<?php

namespace Doornock\ApiModule\Presenters;

use Doornock\Model\DoorModule\DeviceAccessManager;
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
		$doors = $this->deviceManager->findDoorWithAccess($api_key);

		$return = array();

		foreach ($doors as $door) { /** @var Door $door */
			$obj = new \stdClass();
			$obj->id = $door->getId();
			$obj->title = $door->getTitle();
			$obj->access = TRUE; // @todo maybe remove?
			$return[] = $obj;
		}

		$this->sendSuccess($return);
	}


	/** @todo */
	public function actionOpen($api_key, $door_id)
	{
		file_put_contents("A.txt", "YES:" . $api_key . ":" . $door_id);
		$this->sendSuccess(array("Doors opened :D"));
	}

}
