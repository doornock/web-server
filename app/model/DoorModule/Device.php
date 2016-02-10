<?php

namespace Doornock\Model\DoorModule;

use Doctrine\ORM\Mapping as ORM;
use Doornock\Model\UserModule\User;

/**
 * Devices
 *
 * @ORM\Table(name="devices", indexes={@ORM\Index(name="user_id", columns={"owner_id"})})
 * @ORM\Entity(repositoryClass="Doornock\Model\DoorModule\DeviceRepository")
 */
class Device
{
	/**
	 * Type of authentication
	 */
	const UID = 'UID',
		RSA_KEY = 'RSA_KEY',
		NONE = NULL;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="api_key", type="string", length=255, unique=true, nullable=false, options={"comment":"api key, unique for registred device"})
	 */
	private $apiKey;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="description", type="text", length=2000, nullable=true, options={"comment":"users description of device"})
	 */
	private $description;

	/**
	 * Property with auhentication type
	 *
	 * @var string
	 *
	 * @ORM\Column(name="type", type="enum", nullable=true, columnDefinition="enum('UID','RSA_KEY')", options={"comment":"how is device authenticated"})
	 */
	private $type;

	/**
	 * Private key of RSA
	 *
	 * @var string
	 *
	 * @ORM\Column(name="private_key", type="text", nullable=true, options={"comment":"private key of RSA, pure optinal; in base64"})
	 */
	private $privateKey;

	/**
	 * public key of RSA
	 *
	 * @var string
	 *
	 * @ORM\Column(name="public_key", type="text", nullable=true, options={"comment":"public key of RSA; if type column = RSA_KEY should to be filled"})
	 */
	private $publicKey;

	/**
	 * UID by ISO/IEC 14443A
	 *
	 * @var string
	 *
	 * @ORM\Column(name="uid", type="string", length=20, nullable=true, options={"comment":"UID of device; if type column = UID should be filled"})
	 */
	private $uid;

	/**
	 * Device's owner
	 *
	 * @var User
	 *
	 * @ORM\ManyToOne(targetEntity="Doornock\Model\UserModule\User")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=false)
	 * })
	 */
	private $owner;

	/**
	 * Is device blocked (true) or available to authenticate (false)?
	 * @ORM\Column(name="blocked", type="boolean", options={"comment":"is device blocked?"})
	 * @var boolean
	 */
	private $blocked = FALSE;



	/**
	 * Device constructor.
	 * @param User $owner
	 */
	public function __construct(User $owner)
	{
		if ($owner === NULL) {
			throw new NullUserException;
		}
		$this->owner = $owner;
	}


	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * Unique API key
	 * @return string
	 */
	public function getApiKey()
	{
		return $this->apiKey;
	}


	/**
	 * Description of device to user's read
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}


	/**
	 * RSA public key encoded in base64
	 * @return string
	 */
	public function getPublicKey()
	{
		return $this->publicKey;
	}


	/**
	 * Owner of this device
	 * @return User
	 */
	public function getOwner()
	{
		return $this->owner;
	}


	/**
	 * Is device blocked (true) or available to authenticate (false)?
	 * @return boolean
	 */
	public function isBlocked()
	{
		return $this->blocked;
	}


	/**
	 * Set a description of device
	 * @param string $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}


	/**
	 * Change API key by API generator
	 * @param ApiKeyGenerator $generator
	 */
	public function changeApiKey(ApiKeyGenerator $generator)
	{
		$this->apiKey = $generator->generateApiKey();
	}


	/**
	 * Device is blocked and cannot be used
	 */
	public function block()
	{
		$this->blocked = TRUE;
	}


	/**
	 * Device is changed to UID authentication
	 * @param string $uid UID device of ISO/IEC 14443A
	 */
	public function changeUID($uid)
	{
		$this->setType(self::UID);
		$this->uid = $uid;
	}


	/**
	 * Device is changed to UID authentication
	 * @param string $publicKey RSA public key encoded in base64
	 * @param string|null $privateKey RSA private key encoded in base64, no necessary, just if you generate keys on server
	 */
	public function changeRSAKeys($publicKey, $privateKey = NULL)
	{
		$this->setType(self::RSA_KEY);
		$this->publicKey = $publicKey;
		$this->privateKey = $privateKey;
	}


	/**
	 * Method change type and erase data of authentication if type is different
	 * @param String $type
	 */
	private function setType($type) {
		if (!self::checkType($type)) {
			throw new \InvalidArgumentException(sprintf(
				"Entity %s does NOT accept this type of authentication", get_class($this)
			));
		}

		if ($this->type !== $type) {
			$this->privateKey = NULL;
			$this->publicKey = NULL;
			$this->uid = NULL;
		}

		$this->type = $type;
	}


	/**
	 * Check authentication type
	 * @param string $type
	 * @return bool
	 */
	private static function checkType($type)
	{
		static $accepted = array(
			self::UID => TRUE,
			self::RSA_KEY => TRUE,
			self::NONE => TRUE
		);
		return isset($accepted[$type]);
	}

}
