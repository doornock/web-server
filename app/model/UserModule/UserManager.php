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
		$alreadyExist = (bool)$this->userRepository->countBy(array(
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
	 * @todo move to separate class with configuration
	 * @return array [entity instance of User, password => generated password]
	 */
	public function registerRandomCredentials()
	{
		do {
			$username = Nette\Utils\Random::generate(10);
			$password = Nette\Utils\Random::generate(10);

			$alreadyExist = (bool)$this->userRepository->countBy(array(
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


	/**
	 * Method changes user's password
	 * @param User|string $username username if user to change
	 * @param string $newPassword new password to change
	 * @param string|null $actualPassword if not null, checks actual password - see return
	 * @return bool true if password was changed, false if actual password is not verified
	 * @throws UsernameNotFoundException
	 */
	public function changePassword($username, $newPassword, $actualPassword = NULL)
	{
		$user = $this->userRepository->getByUsername($username);
		if ($user === NULL) {
			throw new UsernameNotFoundException($username);
		}

		if ($actualPassword !== NULL && $user->verifyPassword($actualPassword)) {
			return FALSE;
		}

		$user->changePassword($newPassword);

		$this->entityManager->flush($user);

		return TRUE;
	}


	/**
	 * @param User $user
	 * @param $role
	 * @param User|NULL $userBy
	 * @throws ChangeRoleOnSelfNotAllowedException when user want change yourself
	 * @throws MinimumCountAdministratorException when administrator try change
	 */
	public function changeRole(User $user, $role, User $userBy = NULL)
	{
		$admins = $this->userRepository->countBy(array(
			'role' => Roles::ADMINISTRATOR
		));

		if ($user->getId() === $userBy->getId()) {
			throw new ChangeRoleOnSelfNotAllowedException;
		}

		if (in_array(Roles::ADMINISTRATOR, $user->getRoles()) && $role !== Roles::ADMINISTRATOR && $admins === 1) {
			throw new MinimumCountAdministratorException;
		}

		if ($role === Roles::ADMINISTRATOR) {
			$user->mushroomUp();
		} else if ($role === Roles::USER) {
			$user->beNormal();
		} else if ($role === Roles::BLOCKED) {
			$user->block();
		} else {
			throw new UnknownRoleException();
		}

		$this->entityManager->flush($user);


	}

}
