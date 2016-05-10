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
	 * @ORM\ManyToOne(targetEntity="Doornock\Model\DoorModule\Node", inversedBy="doors")
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
	 * Number of GPIO pin
	 *
	 * @ORM\Column(name="gpio_pin", type="integer", nullable=false, options={"comment":"Number of pin"})
	 */
	private $gpioPin;


	/**
	 * Doors in closed on log. zero on GPIO, is true, or false?
	 *
	 * @ORM\Column(name="gpio_closed_on_zero", type="boolean", options={"comment":"GPIO is default on log. 0 or 1"})
	 * @var bool
	 */
	private $gpioClosedOnZero = FALSE;


	/**
	 * Doors in closed on log. zero on GPIO, is true, or false?
	 *
	 * @ORM\Column(name="gpio_is_output", type="boolean", options={"comment":"GPIO is output"})
	 * @var bool
	 */
	private $gpioIsOutput = TRUE;


	/**
	 * Door constructor.
	 * @param Node $node which node control door
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
	 * Get GPIO pin number
	 * @return int
	 */
	public function getGpioPin()
	{
		return $this->gpioPin;
	}


	/**
	 * Doors in closed on log. zero on GPIO, is true, or false?
	 * @return bool
	 */
	public function isGpioClosedOnZero()
	{
		return $this->gpioClosedOnZero;
	}


	/**
	 * Is gpio pin output?
	 * @return boolean
	 */
	public function isGpioOutput()
	{
		return $this->gpioIsOutput;
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


	/**
	 * Setup gpio
	 * @param int $pinNumber set GPIO pin number
	 * @param bool $closedOnZero logic state when door is closed
	 * @param bool $isOutput GPIO is output
	 */
	public function setGpio($pinNumber, $closedOnZero, $isOutput)
	{
		if (!(is_int($pinNumber) && $pinNumber >= 0)) {
			throw new \InvalidArgumentException('GPIO pin has to be positive number');
		}
		$this->gpioPin = $pinNumber;
		$this->gpioClosedOnZero = (bool) $closedOnZero;
		$this->gpioIsOutput = (bool) $isOutput;
	}


}
