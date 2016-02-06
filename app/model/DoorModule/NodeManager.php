<?php

namespace Doornock\Model\DoorModule;


use Doctrine\ORM\EntityManager;
use Nette;


class NodeManager
{

	/** @var NodeRepository */
	private $nodeRepository;


	/** @var EntityManager */
	private $entityManager;


	/**
	 * User manager constructor.
	 * @param NodeRepository $nodeRepository
	 * @param EntityManager $entityManager To propagate new node
	 */
	public function __construct(NodeRepository $nodeRepository, EntityManager $entityManager)
	{
		$this->nodeRepository = $nodeRepository;
		$this->entityManager = $entityManager;
	}



}
