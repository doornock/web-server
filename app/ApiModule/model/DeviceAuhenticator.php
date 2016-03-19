<?php

namespace Doornock\ApiModule\Model;

use Doornock\Model\DoorModule\Device;
use Doornock\Model\DoorModule\DeviceRepository;
use Nette\Http\IRequest;
use Nette\Http\Request;
use Nette\Utils\Strings;

class DeviceAuthenticator
{

	/** @var DeviceRepository */
	private $deviceRepository;

	/** @var int how much can be timestamp different in seconds */
	private $acceptableTime = 10;

	/**
	 * DeviceAuthenticator constructor.
	 * @param DeviceRepository $deviceRepository
	 */
	public function __construct(DeviceRepository $deviceRepository)
	{
		$this->deviceRepository = $deviceRepository;
	}


	/**
	 * Authenticate device to process API requests
	 * @return Device
	 * @throws AuthenticationException if part of process has any problem
	 */
	public function authenticate(IRequest $request)
	{
		$authHeader = $request->getHeader('X-API-Auth-V1');
		$match = Strings::match($authHeader, '#^(?<timestamp>\d+) (?<deviceId>\w+) (?<hash>\w+)$#');
		if (!$match) {
			throw new AuthenticationException(AuthenticationException::REASON_INVALID_INPUT);
		}

		$timestamp = $match['timestamp'];
		$now = time();
		if (!(($now - $this->acceptableTime) < $timestamp && ($now + $this->acceptableTime) > $timestamp)) {
			throw new AuthenticationException(AuthenticationException::REASON_POSSIBLY_REPLAY_ATTACK);
		}

		$device = $this->deviceRepository->getDeviceById($match['deviceId']);
		if (!$device) {
			throw new AuthenticationException(AuthenticationException::REASON_RELATED_OBJECT_NOT_FOUND);
		}

		$requestPath = $request->getMethod() . " " . $request->getUrl()->getPath();
		$body = $request->getRawBody();
		$hmacBody = $timestamp . "|" . $requestPath . "|" . $body;

		$calculated = hash_hmac('sha256', $hmacBody, $device->getApiKey());

		if ($match['hash'] !== $calculated) {
			throw new AuthenticationException(AuthenticationException::REASON_VERIFICATION);
		}

		if ($device->isBlocked()) {
			throw new AuthenticationException(AuthenticationException::REASON_BLOCKED);
		}

		return $device;
	}

}