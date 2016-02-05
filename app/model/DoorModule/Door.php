<?php

namespace Doornock\Model\DoorModule;

use Doctrine\ORM\Mapping as ORM;

/**
 * Doors
 *
 * @ORM\Table(name="doors", indexes={@ORM\Index(name="node_id", columns={"node_id"})})
 * @ORM\Entity
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



}

