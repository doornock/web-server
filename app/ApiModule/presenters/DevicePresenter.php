<?php

namespace Doornock\ApiModule\Presenters;

use Doornock\Model\DoorModule\Device;
use Doornock\Model\DoorModule\DeviceAccessManager;
use Doornock\Model\DoorModule\DeviceManager;
use Doornock\Model\DoorModule\DeviceRepository;
use Doornock\Model\UserModule\User;
use Doornock\Model\UserModule\UserRepository;
use Nette;


class DevicePresenter extends BasePresenter
{

	/** @var DeviceRepository */
	private $deviceRepository;


	/** @var DeviceAccessManager */
	private $deviceAccessManager;


	/** @var DeviceManager */
	private $deviceManager;


	/** @var UserRepository */
	private $userRepository;

	/**
	 * DevicePresenter constructor.
	 * @param DeviceRepository $deviceRepository
	 * @param DeviceAccessManager $deviceAccessManager
	 * @param DeviceManager $deviceManager
	 * @param UserRepository $userRepository
	 */
	public function __construct(
		DeviceRepository $deviceRepository,
		DeviceAccessManager $deviceAccessManager,
		DeviceManager $deviceManager,
		UserRepository $userRepository
	)
	{
		parent::__construct();
		$this->deviceRepository = $deviceRepository;
		$this->deviceAccessManager = $deviceAccessManager;
		$this->deviceManager = $deviceManager;
		$this->userRepository = $userRepository;
	}


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
	public function actionRegister($username, $password)
	{
		$description = $this->request->getPost('description');
		$publicKey = $this->request->getPost('public_key');

		$user = $this->userRepository->getByUsername($username);
		if (!$user || !$user->verifyPassword($password)) {
			$this->sendRequestError(401, "Bad username or password");
		}

		$device = $this->deviceManager->addDeviceRSA($user, $publicKey, $description); /** @var $device Device */

		$this->sendSuccess(array(
			'device_id' => $device->getId(),
			'api_key' => $device->getApiKey()
		));

	}


	/**
	 * Method update information about device
	 * @param $api_key
	 * @throws \Doornock\Model\DoorModule\ApiKeyNotFoundException
	 */
	public function actionUpdate($api_key)
	{
		$description = $this->request->getPost('description');
		$publicKey = $this->request->getPost('public_key');

		$this->deviceManager->updateRSAKeyDeviceByApi($api_key, $publicKey, $description);

		$this->sendSuccess();
	}


}
