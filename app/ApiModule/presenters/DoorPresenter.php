<?php

namespace Doornock\ApiModule\Presenters;

use Nette;


class DoorPresenter extends BasePresenter
{

	/** @todo */
	public function actionList($api_key)
	{
		$doors = array();
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
