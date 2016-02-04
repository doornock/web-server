<?php

namespace Doornock\ApiModule\Presenters;

use Doornock\Model\DoorModule\Device;
use Doornock\Model\DoorModule\DeviceManager;
use Doornock\Model\DoorModule\DeviceRepository;
use Doornock\Model\UserManager;
use Doornock\Model\UserModule\User;
use Doornock\Model\UserModule\UserRepository;
use Nette;


class DevicePresenter extends BasePresenter
{

	/** @var DeviceRepository */
	public $deviceRepository;


	/** @var DeviceManager */
	public $deviceManager;


	/** @var UserRepository */
	public $userRepository;



	/**
	 * @todo refactor to API scheme
	 * @param string $deviceId
	 */
	public function actionGetPublicKey($deviceId)
	{
		$device = $this->deviceRepository->find($deviceId); /** @var $device Device */
		$this->sendResponse(
			new Nette\Application\Responses\TextResponse(
				$device->getPublicKey()
			)
		);
	}


	/**
	 * Register device and return API key
	 * @param string $username
	 * @param string $password
	 */
	public function actionRegisterDevice($username, $password)
	{
		$description = $this->request->getPost('description');
		$publicKey = $this->request->getPost('public_key');

		$user = $this->userRepository->find($username); /** @var $user User */
		if (!$user || !$user->verifyPassword($password)) {
			$this->sendRequestError(401, "Bad username or password");
		}

		$device = $this->deviceManager->addDeviceRSA($user, $publicKey, $description); /** @var $device Device */

		$this->sendSuccess(array(
			'device_id' => $device->getId(),
			'api_key' => $device->getApiKey()
		));

	}

	public function actionUpdateDevice($api_key)
	{
		$description = $this->request->getPost('description');
		$publicKey = $this->request->getPost('public_key');

		$this->deviceManager->updateRSAKeyDeviceByApi($api_key, $publicKey);

		$this->sendSuccess();
	}


	/** @todo */
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

		$this->sendSuccess($doors);
	}


	/** @todo */
	public function actionOpenDoor($api_key, $door_id)
	{
		file_put_contents("A.txt", "YES:" . $api_key . ":" . $door_id);
		$this->sendSuccess(array("Doors opened :D"));
	}


}
