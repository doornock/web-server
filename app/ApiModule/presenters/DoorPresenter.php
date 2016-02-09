<?php

namespace Doornock\ApiModule\Presenters;

use Doornock\Model\DoorModule\DeviceAccessManager;
use Doornock\Model\DoorModule\Door;
use Nette;


class DoorPresenter extends BasePresenter
{

	/** @var DeviceAccessManager */
	private $deviceManager;

	public function actionList($api_key)
	{
		$doors = $this->deviceManager->findDoorWithAccess($api_key);

		$doors = array();

		foreach ($doors as $door) { /** @var Door $door */
			$obj = new \stdClass();
			$obj->id = $door->getId();
			$obj->title = $door->getTitle();
			$obj->access = TRUE; // @todo maybe remove?
			$doors[] = $obj;
		}

		/** @todo */
		for ($i = 0; $i < 5; $i++) {
			$obj = new \stdClass();
			$obj->id = $i;
			$obj->title = "Dveře" . $i;
			$obj->access = true;
			$doors[] = $obj;
		}

		$this->sendSuccess($doors);
	}


	/** @todo */
	public function actionOpen($api_key, $door_id)
	{
		file_put_contents("A.txt", "YES:" . $api_key . ":" . $door_id);
		$this->sendSuccess(array("Doors opened :D"));
	}

}
