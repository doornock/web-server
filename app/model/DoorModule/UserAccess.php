<?php

namespace Doornock\Model\DoorModule;


use Doctrine\ORM\Mapping as ORM;
use Doornock\Model\UserModule\User;


/**
 * Many to many associative entity, in future will be used to define restriction
 * @ORM\Table(name="doors_acl")
 * @ORM\Entity
 */
class UserAccess
{


	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;


	/**
	 * User
	 * @ORM\ManyToOne(targetEntity="Doornock\Model\UserModule\User")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	 * })
	 * @var User
	 */
	private $user;


	/**
	 * Door
	 * @ORM\ManyToOne(targetEntity="Doornock\Model\DoorModule\Door")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="door_id", referencedColumnName="id", nullable=false)
	 * })
	 * @var Door
	 */
	private $door;



	/**
	 * Has access to door?
	 *
	 * @var bool
	 *
	 * @ORM\Column(name="access", type="boolean", options={"comment":"name of terminal"})
	 */
	private $access = TRUE;


}