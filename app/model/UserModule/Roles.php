<?php

namespace Doornock\Model\UserModule;

class Roles
{

	CONST USER = 'user',
		ADMINISTRATOR = 'administrator',
		BLOCKED = 'blocked';


	/**
	 * Return array (key => value), where key is code of role, and value is human readable title
	 * @return array
	 */
	public function findRolesWithTitle()
	{
		return array(
			self::USER => 'User',
			self::ADMINISTRATOR => 'Administrator',
			self::BLOCKED => 'Blocked'
		);
	}

}
