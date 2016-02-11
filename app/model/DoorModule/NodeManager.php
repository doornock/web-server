<?php

namespace Doornock\Model\DoorModule;


use Doctrine\ORM\EntityManager;
use Nette;


/**
 * Service layer communicates with nodes
 */
class NodeManager implements ApiKeyGenerator
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


	/**
	 * Add new node and register them doors
	 * @param string $title
	 * @param array[doorId=>title] $doors
	 * @param bool $nfcAvailable
	 * @return Node
	 */
	public function addNode($title, array $doors = array(), $nfcAvailable = TRUE)
	{
		$node = new Node();
		$node->setTitle($title);
		$node->regenerateApiKey($this);
		$node->setAvailabilityNfc($nfcAvailable);
		$this->entityManager->persist($node);

		foreach ($doors as $id => $title) {
			$door = new Door($node, $id, $title);
			$this->entityManager->persist($door);
		}

		$this->entityManager->flush($node);

		return $node;
	}
	}



	/**
	 * Generate free API key for node
	 *
	 * @use only as ApiKeyGenerator
	 *
	 * @return string
	 */
	public function generateApiKey()
	{
		do {
			$apiKey = Nette\Utils\Random::generate(50, 'a-zA-Z0-9-,');
			$exists = (bool) $this->nodeRepository->countBy(array(
				"apiKey" => $apiKey
			));
		} while ($exists);
		return $apiKey;
	}

}
