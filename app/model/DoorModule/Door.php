<?php

namespace Doornock\Model\DoorModule;

use Doctrine\ORM\Mapping as ORM;

/**
 * Doors
 *
 * @ORM\Table(name="doors", indexes={@ORM\Index(name="node_id", columns={"node_id"})})
 * @ORM\Entity(repositoryName="Doornock\Model\DoorModule\DoorRepository")
 */
class Door
{
	/**
	 * @var string
	 *
	 * @ORM\Column(name="id", type="string", length=100, nullable=false, options={"comment":"defined by node"})
	 * @ORM\Id
	 */
	private $id;


	/**
	 * Where is door connected
	 *
	 * @var Node
	 *
	 * @ORM\ManyToOne(targetEntity="Doornock\Model\DoorModule\Node")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="node_id", referencedColumnName="id")
	 * })
	 */
	private $node;


	/**
	 * Opening time in milliseconds, it says how long a door will be unlocked after signal, default time is 3s
	 * @ORM\Column(name="opening_time", type="int", nullable=false, options={"comment":"opening time in milliseconds"})
	 * @var Door
	 */
	private $openingTime = 3000;

	/**
	 * Door constructor.
	 * @param Node $node
	 * @param string $id
	 */
	public function __construct(Node $node, $id)
	{
		$this->node = $node;
		$this->id = $id;
	}


	/**
	 * Opening time in milliseconds, it says how long a door will be unlocked after signal, default time is 3s
	 * @param Door $openingTime
	 */
	public function setDefaultOpeningTime($openingTime)
	{
		$this->openingTime = $openingTime;
	}

}
