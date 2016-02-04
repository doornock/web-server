<?php

namespace Doornock\Model;

use Nette;
use Nette\Security\Passwords;


/**
 * Users management.
 */
class UserManager extends Nette\Object
{
	const
		TABLE_NAME = 'users',
		COLUMN_ID = 'id',
		COLUMN_NAME = 'username',
		COLUMN_PASSWORD_HASH = 'password',
		COLUMN_ROLE = 'role';


	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}


	public function registerUser()
	{
		$username = Nette\Utils\Random::generate(5);
		$password = Nette\Utils\Random::generate(100);
		$this->add($username, $password);
		return array(
			'username' => $username,
			'password' => $password
		);
	}


	public function addDevice($username, $password, $publicKey, $description)
	{
		$apiKey = Nette\Utils\Random::generate(100);

		$user = $this->authenticate(array($username, $password));
		$device = $this->database->table("devices")->insert(array(
			'owner_id' => $user->id,
			'description' => $description,
			'public_key' => $publicKey,
			'api_key' => $apiKey,
			'type' => 'RSA_KEY'
		))->id;
		return array(
			'device_id' => $device,
			'api_key' => $apiKey
		);
	}


	public function getPublicKey($deviceId)
	{
		$row = $this->database->table('devices')->get($deviceId);
		return $row ? $row->public_key : NULL;
	}



	public function updatePublicKey($apiKey, $publicKey)
	{
		$this->database->table('devices')->where('api_key', $apiKey)->update(array(
			'public_key' => $publicKey
		));
	}


}



class DuplicateNameException extends \Exception
{}
