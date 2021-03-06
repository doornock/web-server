<?php

namespace Doornock\Model\UserModule;

use Doctrine\ORM\Mapping as ORM;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;

/**
 * Users
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Doornock\Model\UserModule\UserRepository")
 */
class User implements IIdentity
{
	/**
	 * Unique system identifier
	 *
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * Unique authentication identifier
	 *
	 * @var string
	 *
	 * @ORM\Column(name="username", type="string", length=100, nullable=false, unique=true)
	 */
	private $username;

	/**
	 * Hashed password
	 *
	 * @var string
	 *
	 * @ORM\Column(name="password", type="string", length=100, nullable=true)
	 */
	private $password;

	/**
	 * Which roles has user
	 *
	 * @var string
	 *
	 * @ORM\Column(name="role", type="enum", columnDefinition="enum('user','administrator','blocked')", nullable=false)
	 */
	private $role = Roles::USER;

	/**
	 * Constructor
	 */
	public function __construct($username)
	{
		$this->username = $username;
	}

	/**
	 * Unique identifier of user in system
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Returns username of user
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}


	/**
	 * Set user password, one way.
	 * @param string $password new password
	 */
	public function changePassword($password)
	{
		$this->password = Passwords::hash($password);
	}



	/**
	 * Checks input password is same as in storage
	 * @param string $password
	 * @return bool
	 */
	public function verifyPassword($password)
	{
		return Passwords::verify($password, $this->password);
	}


	/**
	 * List of roles, which is in
	 * @return array[string]
	 */
	public function getRoles()
	{
		return [$this->role];
	}


	/**
	 * Block user
	 */
	public function block()
	{
		$this->role = Roles::BLOCKED;
	}


	/**
	 * Unblock user or change administrator to user
	 */
	public function beNormal()
	{
		$this->role = Roles::USER;
	}


	/**
	 * Change user to administrator
	 */
	public function mushroomUp()
	{
		$this->role = Roles::ADMINISTRATOR;
	}

}

