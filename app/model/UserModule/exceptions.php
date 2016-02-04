<?php

namespace Doornock\Model\UserModule;


/**
 * Class used to indicate username is taken while registration
 * @package Doornock\Model\UserModule
 */
class UsernameAlreadyRegisteredException extends \Exception
{
	private $username;

	/**
	 * UsernameAlreadyRegisteredException constructor.
	 * @param $username
	 */
	public function __construct($username, $code = 0, \Exception $previous = null)
	{
		parent::__construct(sprintf("Username '%s' is already registred", $username));
		$this->username = $username;
	}

	/**
	 * Existring username
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}

}