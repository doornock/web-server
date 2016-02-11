<?php

namespace Doornock\Model\DoorModule;

use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\Strings;

/**
 * Doors
 *
 * @ORM\Table(name="doors", indexes={@ORM\Index(name="node_id", columns={"node_id"})})
 * @ORM\Entity(repositoryClass="Doornock\Model\DoorModule\DoorRepository")
 */
class Door
{
	/**
	 * Universal identification of doors
	 *
	 * @var int
	 *
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;


	/**
	 * Where is door connected
	 *
	 * @var Node
	 *
	 * @ORM\ManyToOne(targetEntity="Doornock\Model\DoorModule\Node", inversedBy="doors", cascade={"remove"})
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="node_id", referencedColumnName="id", onDelete="CASCADE")
	 * })
	 */
	private $node;


	/**
	 * Human readable name
	 *
	 * @ORM\Column(name="title", type="string", length=100)
	 * @var string
	 */
	private $title;


	/**
	 * Opening time in milliseconds, it says how long a door will be unlocked after signal, default time is 3s
	 * @ORM\Column(name="opening_time", type="integer", nullable=false, options={"comment":"opening time in milliseconds"})
	 * @var int
	 */
	private $openingTime = 3000;

	/**
	 * Door constructor.
	 * @param Node $node which node control door
	 * @param string $code identifier on node
	 * @param string|NULL $title human readable name
	 */
	public function __construct(Node $node, $title = NULL)
	{
		$this->setNode($node);
		$this->title = $title;
	}


	/**
	 * Return unique id for all doors
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * Node which control door
	 * @return Node
	 */
	public function getNode()
	{
		return $this->node;
	}


	/**
	 * Human readable name (eg. "Main house doors")
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}


	/**
	 * Opening time in milliseconds, it says how long a door will be unlocked after signal
	 * @return int
	 */
	public function getOpeningTime()
	{
		return $this->openingTime;
	}


	/**
	 * Opening time in milliseconds, it says how long a door will be unlocked after signal, default time is 3s
	 * @param int $openingTime
	 */
	public function setDefaultOpeningTime($openingTime)
	{
		$this->openingTime = $openingTime;
	}


	/**
	 * Change title for this door
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}


	/**
	 * Change node controlling this door
	 * @param Node $node
	 */
	public function setNode(Node $node)
	{
		$this->node = $node;
	}


}
