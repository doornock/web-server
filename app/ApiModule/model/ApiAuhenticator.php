<?php

namespace Doornock\ApiModule\Model;

use Doornock\Model\DoorModule\Device;
use Doornock\Model\DoorModule\DeviceRepository;
use Doornock\Model\DoorModule\NodeRepository;
use Nette\Http\IRequest;
use Nette\Http\Request;
use Nette\Utils\Strings;

class ApiAuthenticator
{

	/** @var DeviceRepository */
	private $deviceRepository;

	/** @var NodeRepository */
	private $nodeRepository;

	/** @var int how much can be timestamp different in seconds */
	private $acceptableTime = 10;

	/**
	 * DeviceAuthenticator constructor.
	 * @param DeviceRepository $deviceRepository
	 * @param NodeRepository $nodeRepository
	 */
	public function __construct(DeviceRepository $deviceRepository, NodeRepository $nodeRepository)
	{
		$this->deviceRepository = $deviceRepository;
		$this->nodeRepository = $nodeRepository;
	}


	/**
	 * Authenticate device to process API requests
	 * @return Device
	 * @throws AuthenticationException if part of process has any problem
	 */
	public function authenticateDevice(IRequest $request)
	{
		$match = $this->matchHeader($request);
		$device = $this->deviceRepository->getDeviceById($match['id']);
		if (!$device) {
			throw new AuthenticationException(AuthenticationException::REASON_RELATED_OBJECT_NOT_FOUND);
		}

		$this->verifyHash($request, $device->getApiKey(), $match['timestamp'], $match['hash']);

		return $device;
	}

	/**
	 * Authenticate device to process API requests
	 * @return Device
	 * @throws AuthenticationException if part of process has any problem
	 */
	public function authenticateNode(IRequest $request)
	{
		$match = $this->matchHeader($request);
		$node = $this->nodeRepository->getById($match['id']);
		if (!$node) {
			throw new AuthenticationException(AuthenticationException::REASON_RELATED_OBJECT_NOT_FOUND);
		}

		$this->verifyHash($request, $node->getApiKey(), $match['timestamp'], $match['hash']);

		return $node;
	}


	/**
	 * Match API auth header
	 * @param IRequest $request
	 * @return array [timestamp, id, hash]
	 * @throws AuthenticationException
	 */
	private function matchHeader(IRequest $request)
	{
		$authHeader = $request->getHeader('X-API-Auth-V1');
		$match = Strings::match($authHeader, '#^(?<timestamp>\d+) (?<id>\w+) (?<hash>\w+)$#');
		if (!$match) {
			throw new AuthenticationException(AuthenticationException::REASON_INVALID_INPUT);
		}

		$timestamp = $match['timestamp'];
		$now = time();
		if (!(($now - $this->acceptableTime) < $timestamp && ($now + $this->acceptableTime) > $timestamp)) {
			throw new AuthenticationException(AuthenticationException::REASON_POSSIBLY_REPLAY_ATTACK);
		}
		return $match;
	}


	/**
	 * Verify hash
	 * @param IRequest $request
	 * @param string $apiKey of resource
	 * @param string|int $timestamp from header
	 * @param string $hash from header
	 * @throws AuthenticationException
	 */
	private function verifyHash(IRequest $request, $apiKey, $timestamp, $hash)
	{
		$requestPath = $request->getMethod() . " " . $request->getUrl()->getPath();
		$body = $request->getRawBody();
		$hmacBody = $timestamp . "|" . $requestPath . "|" . $body;

		$calculated = hash_hmac('sha256', $hmacBody, $apiKey);

		if ($hash !== $calculated) {
			throw new AuthenticationException(AuthenticationException::REASON_VERIFICATION);
		}
	}


}