<?php

namespace Doornock\Model\DoorModule;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\Validators;

/**
 * Nodes
 *
 * @ORM\Table(name="nodes")
 * @ORM\Entity(repositoryClass="Doornock\Model\DoorModule\NodeRepository")
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
	 * @ORM\Column(name="auth_key", type="string", length=255, nullable=false, options={"comment":"key to authenticate with server"})
	 */
	private $apiKey;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="title", type="string", length=255, nullable=true, options={"comment":"name of terminal"})
	 */
	private $title;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="available_nfc", type="boolean", nullable=false, options={"comment":"has NFC reader?"})
	 */
	private $availableNfc;


	/**
	 * @ORM\OneToMany(targetEntity="Doornock\Model\DoorModule\Door", mappedBy="node")
	 * @var Collection
	 */
	private $doors;


	/**
	 * @ORM\Column(name="http_ip_address", type="string", nullable=false, options={"comment":"API http URL"})
	 * @var string
	 */
	private $apiEndpointUrl;



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
	public function getApiKey()
	{
		return $this->apiKey;
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

	/**
	 * Regenerate API key
	 * @param ApiKeyGenerator $apiKeyGenerator
	 */
	public function regenerateApiKey(ApiKeyGenerator $apiKeyGenerator)
	{
		$this->apiKey = $apiKeyGenerator->generateApiKey();
	}


	/**
	 * Returns attached door
	 * @return array
	 */
	public function getDoors()
	{
		return $this->doors->getValues();
	}

	/**
	 * HTTP API URL, if it is standard client, contain: http://{ip/hostname}:{port}/
	 * @return string|null
	 */
	public function getApiEndpointUrl()
	{
		return $this->apiEndpointUrl;
	}

	/**
	 * HTTP API URL, if it is standard client, contain: http://{ip/hostname}:{port}/
	 * @example http://192.168.3.2:5555/
	 * @param string $apiEndpointUrl
	 */
	public function setApiEndpointUrl($apiEndpointUrl)
	{
		if (!Validators::isUrl($apiEndpointUrl)) {
			throw new \InvalidArgumentException('Api endpoint is not valid URL');
		}
		$this->apiEndpointUrl = $apiEndpointUrl;
	}


}
