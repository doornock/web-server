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
	 * @todo refactor to collection, for multiple groups
	 *
	 * @var string
	 *
	 * @ORM\Column(name="role", type="string", nullable=true)
	 */
	private $role;

/*
	/**
	 * @var \Doctrine\Common\Collections\Collection
	 *
	 * @ORM\ManyToMany(targetEntity="Doornock\Model\Entities\Door", mappedBy="user")
	 * @ORM\JoinTable(name="doors_acl",
	 *   joinColumns={
	 *     @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 *   },
	 *   inverseJoinColumns={
	 *     @ORM\JoinColumn(name="door_id", referencedColumnName="id")
	 *   }
	 * )
	 * /
	private $doorWithAccess;
*/


	/**
	 * Constructor
	 */
	public function __construct($username)
	{
		$this->doorWithAccess = new \Doctrine\Common\Collections\ArrayCollection();
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

}

