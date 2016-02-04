<?php

namespace Doornock\FrontModule\Presenters;

use Doornock\Model\UserManager;
use Nette;


class ApiPresenter extends BasePresenter
{

	/** @var UserManager @inject */
	public $userManager;


	public function actionKnockKnock()
	{
		$this->sendResponse(new Nette\Application\Responses\JsonResponse(array(
			'status' => "OK",
			'data' => array(
				'site' =>
					array(
						'guid' => 'f5bdf871-20a7-4bc0-865b-e7a1a56b6a43',
						'title' => 'DOORNOCK HQ-dev',
						'registration-allowed' => true
					)
			)
		)));
	}


	public function actionRegister()
	{
		$data = $this->userManager->registerUser();
		$this->sendResponse(new Nette\Application\Responses\JsonResponse(array(
			'status' => "OK",
			'data' => $data
		)));
	}

	public function actionGetPublicKey($deviceId)
	{
		$data = $this->userManager->getPublicKey($deviceId);
		$this->sendResponse(new Nette\Application\Responses\TextResponse($data));
	}


	public function actionAddDevice($username, $password)
	{
		$description = $this->request->getPost('description');
		$publicKey = $this->request->getPost('public_key');

		/*
		file_put_contents("out.txt", "OUT:\n", FILE_TEXT | FILE_APPEND);
		file_put_contents("out.txt", serialize($_FILES), FILE_TEXT | FILE_APPEND);
		file_put_contents("out.txt", serialize($_POST), FILE_TEXT | FILE_APPEND);
		file_put_contents("out.txt",  "\n\n". file_get_contents("php://input") . "\n\n", FILE_TEXT | FILE_APPEND);

		file_put_contents("out.txt", "\n---:\n", FILE_TEXT | FILE_APPEND);
		exit;

		$input = json_decode(file_get_contents("php://input"));
		$description = $input['description'];
		$publicKey = $input['public_key'];
*/

		$data = $this->userManager->addDevice($username, $password, $publicKey, $description);
		$this->sendResponse(new Nette\Application\Responses\JsonResponse(array(
			'status' => "OK",
			'data' => $data
		)));
	}

	public function actionUpdateDevice($api_key)
	{
		$description = $this->request->getPost('description');
		$publicKey = $this->request->getPost('public_key');
		$this->userManager->updatePublicKey($api_key, $publicKey);
		$this->sendResponse(new Nette\Application\Responses\JsonResponse(array(
			'status' => "OK",
		)));
	}


	public function actionDoorsList($api_key)
	{
		$doors = array();
		for ($i = 0; $i < 5; $i++) {
			$obj = new \stdClass();
			$obj->id = $i;
			$obj->title = "Dveře" . $i;
			$obj->access = true;
			$doors[] = $obj;

		}

		$this->sendResponse(new Nette\Application\Responses\JsonResponse(array(
			'status' => "OK",
			'data' => $doors
		)));
	}


	public function actionOpenDoor($api_key, $door_id)
	{
		file_put_contents("A.txt", "YES:" . $api_key . ":" . $door_id);
		$this->sendResponse(new Nette\Application\Responses\JsonResponse(array(
			'status' => "OK"
		)));
	}


}
