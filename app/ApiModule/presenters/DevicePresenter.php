<?php

namespace Doornock\ApiModule\Presenters;

use Doornock\ApiModule\Model\AuthenticationException;
use Doornock\ApiModule\Model\DeviceAuthenticator;
use Doornock\Model\DoorModule\Device;
use Doornock\Model\DoorModule\DeviceAccessFasade;
use Doornock\Model\DoorModule\DeviceManager;
use Doornock\Model\DoorModule\DeviceRepository;
use Doornock\Model\UserModule\User;
use Doornock\Model\UserModule\UserRepository;
use Nette;


class DevicePresenter extends BasePresenter
{

	/** @var DeviceRepository */
	private $deviceRepository;


	/** @var DeviceAccessFasade */
	private $deviceAccessManager;


	/** @var DeviceManager */
	private $deviceManager;


	/** @var UserRepository */
	private $userRepository;


	/** @var DeviceAuthenticator */
	private $deviceAuthenticator;

	/**
	 * DevicePresenter constructor.
	 * @param DeviceRepository $deviceRepository
	 * @param DeviceAccessFasade $deviceAccessManager
	 * @param DeviceManager $deviceManager
	 * @param UserRepository $userRepository
	 */
	public function __construct(
		DeviceRepository $deviceRepository,
		DeviceAccessFasade $deviceAccessManager,
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


	public function startup()
	{
		parent::startup();
		$this->deviceAuthenticator = new DeviceAuthenticator($this->deviceRepository);
	}


	/**
	 * Register device and return API key
	 */
	public function actionRegister()
	{
		$params = $this->getRequestPostParams();

		$user = $this->userRepository->getByUsername($params->requireParamString('username'));
		if (!$user || !$user->verifyPassword($params->requireParamString('password'))) {
			$this->sendRequestError(401, "Bad username or password");
		}

		$device = $this->deviceManager->addDeviceRSA(
			$user,
			$params->requireParamString('public_key'),
			$params->requireParamString('description')
		); /** @var $device Device */

		$this->sendSuccess(array(
			'device_id' => $device->getId(),
			'api_key' => $device->getApiKey()
		));

	}


	/**
	 * Method update information about device
	 */
	public function actionUpdate()
	{
		$params = $this->getRequestPostParams();

		try {
			$device = $this->deviceAuthenticator->authenticate($this->getHttpRequest());
			$this->deviceManager->updateRSAKeyDeviceByApi(
				$device->getId(),
				$params->requireParamString('public_key')
			);
			$this->sendSuccess();
		} catch (AuthenticationException $e) {
			$this->sendRequestError($e->isAuthorizationProblem() ? 403 : 401, "Authentication failed", $e->getCode());
		}
	}


}
