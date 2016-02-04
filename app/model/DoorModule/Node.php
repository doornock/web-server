<?php

namespace Doornock\Model\DoorModule;

use Doctrine\ORM\Mapping as ORM;

/**
 * Nodes
 *
 * @ORM\Table(name="nodes")
 * @ORM\Entity
 */
class Node
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
	 * @var string
	 *
	 * @ORM\Column(name="auth_key", type="string", length=255, nullable=false)
	 */
	private $authKey;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="title", type="string", length=255, nullable=true)
	 */
	private $title;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="available_nfc", type="boolean", nullable=false)
	 */
	private $availableNfc;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getAuthKey()
	{
		return $this->authKey;
	}

	/**
	 * Get title
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Has node NFC reader and is available?
	 * @return boolean
	 */
	public function isAvailableNfc()
	{
		return $this->availableNfc;
	}

	/**
	 * Nodes title to see by user
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * Change availability on node
	 * @param boolean $availableNfc
	 */
	public function setAvailabilityNfc($availableNfc)
	{
		if (!is_bool($availableNfc)) {
			throw new \InvalidArgumentException("Argument \$availableNfc must be bool, got " . gettype($availableNfc));
		}
		$this->availableNfc = $availableNfc;
	}


}

