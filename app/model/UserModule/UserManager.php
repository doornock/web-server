<?php

namespace Doornock\Model\UserModule;


use Kdyby\Doctrine\EntityManager;
use Nette;

class UserManager
{

	/** @var UserRepository */
	private $userRepository;


	/** @var EntityManager */
	private $entityManager;


	/**
	 * User manager constructor.
	 * @param UserRepository $userRepository To find users
	 * @param EntityManager $entityManager To register user
	 */
	public function __construct(UserRepository $userRepository, EntityManager $entityManager)
	{
		$this->userRepository = $userRepository;
		$this->entityManager = $entityManager;
	}


	/**
	 * Register user by credentials
	 * @param string $username
	 * @param string $password
	 * @return User
	 * @throws UsernameAlreadyRegisteredException throws if username is already taken
	 */
	public function register($username, $password = null)
	{
		$alreadyExist = (bool) $this->userRepository->countBy(array(
			'username' => $username
		));
		if ($alreadyExist) {
			throw new UsernameAlreadyRegisteredException($username);
		}

		$user = new User($username);
		$user->changePassword($password);

		$this->entityManager->persist($user);
		$this->entityManager->flush();

		return $user;
	}



	/**
	 * Generate user credentials and persist them
	 * @return array [entity instance of User, password => generated password]
	 */
	public function registerRandomCredentials()
	{
		do {
			$username = Nette\Utils\Random::generate(10);
			$password = Nette\Utils\Random::generate(100);

			$alreadyExist = (bool) $this->userRepository->countBy(array(
				'username' => $username
			));
		} while ($alreadyExist);

		$user = new User($username);
		$user->changePassword($password);

		$this->entityManager->persist($user);
		$this->entityManager->flush();

		return array(
			'entity' => $user,
			'password' => $password
		);
	}

}