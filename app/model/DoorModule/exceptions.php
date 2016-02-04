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