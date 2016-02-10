<?php

namespace Doornock\Model\DoorModule;

use Exception;

class NullUserException extends \InvalidArgumentException
{}

class ApiKeyNotFoundException extends \Exception
{
	private $apiKey;

	public function __construct($apiKey, $code = 0, Exception $previous = null)
	{
		$this->apiKey = $apiKey;
		parent::__construct("API key does not exist", $code, $previous);
	}

	/**
	 * @return string
	 */
	public function getApiKey()
	{
		return $this->apiKey;
	}

}

class DeviceIsBlockedException extends \Exception
{
	private $device;

	public function __construct(Device $device, $code = 0, Exception $previous = null)
	{
		$this->device = $device;
		parent::__construct("Device is blocked", $code, $previous);
	}

	/**
	 * Object of device
	 * @return Device
	 */
	public function getDevice()
	{
		return $this->device;
	}

}

class AccessUnauthorizedException extends \Exception
{

	/** While authentication device not found */
	const DEVICE_NOT_FOUND = 1;

	/** User is blocked */
	const USER_BLOCKED = 2;

	/** Device is blocked */
	const DEVICE_BLOCKED = 4;

	/** Component to authentication declines verification */
	const VERIFICATION_FAILED = 8;


	/**
	 * AccessUnauthorizedException constructor.
	 * @param int $reason reason see constants above
	 * @param string $message detail about declines
	 * @param Exception $previous
	 */
	public function __construct($reason, $message = NULL, Exception $previous = NULL)
	{
		parent::__construct($message, $reason, $previous);
	}

}

class DeviceNotFoundException extends \Exception
{}
