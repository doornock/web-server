<?php

namespace Doornock\ApiModule\Presenters;

use Doornock\ApiModule\Model\AuthenticationException;
use Doornock\ApiModule\Model\ApiAuthenticator;
use Doornock\Model\DoorModule\Device;
use Doornock\Model\DoorModule\DeviceAccessManager;
use Doornock\Model\DoorModule\DeviceManager;
use Doornock\Model\UserModule\UserRepository;
use Nette;


class DevicePresenter extends BasePresenter
{

	/** @var DeviceAccessManager */
	private $deviceAccessManager;


	/** @var DeviceManager */
	private $deviceManager;


	/** @var UserRepository */
	private $userRepository;


	/** @var ApiAuthenticator */
	private $deviceAuthenticator;

	/**
	 * DevicePresenter constructor.
	 * @param DeviceAccessManager $deviceAccessManager
	 * @param DeviceManager $deviceManager
	 * @param UserRepository $userRepository
	 * @param ApiAuthenticator $apiAuthenticator
	 */
	public function __construct(
		DeviceAccessManager $deviceAccessManager,
		DeviceManager $deviceManager,
		UserRepository $userRepository,
		ApiAuthenticator $apiAuthenticator
	)
	{
		parent::__construct();
		$this->deviceAccessManager = $deviceAccessManager;
		$this->deviceManager = $deviceManager;
		$this->userRepository = $userRepository;
		$this->deviceAuthenticator = $apiAuthenticator;
	}


	public function startup()
	{
		parent::startup();
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
		), $device->getApiKey());

	}


	/**
	 * Method update information about device
	 */
	public function actionUpdate()
	{
		$params = $this->getRequestPostParams();

		try {
			$device = $this->deviceAuthenticator->authenticateDevice($this->getHttpRequest());
			$this->deviceManager->updateRSAKeyDeviceByApi(
				$device->getId(),
				$params->requireParamString('public_key')
			);
			$this->sendSuccess([], $device->getApiKey());
		} catch (AuthenticationException $e) {
			$this->sendRequestError($e->isAuthorizationProblem() ? 403 : 401, "Authentication failed", $e->getCode());
		}
	}


}
