<?php

namespace Doornock\Model\UserModule;

use Nette;
use Nette\Security\IAuthenticator;
use Nette\Security\AuthenticationException;

class Authenticator implements IAuthenticator
{

	/** @var UserRepository */
	private $userRepository;

	/**
	 * Authenticator constructor.
	 * @param UserRepository $userRepository
	 */
	public function __construct(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
	}


	/**
	 * Authenticate user by username and password
	 * @param array $credentials - array with two element [username, password]
	 * @return User
	 * @throws AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;

		$user = $this->userRepository->findOneBy(array(
			'username' => $username
		)); /** @var $user User */

		if ($user === NULL) {
			throw new Nette\Security\AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
		}

		if (!$user->verifyPassword($password)) {
			throw new Nette\Security\AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
		}

		return $user;
	}

}