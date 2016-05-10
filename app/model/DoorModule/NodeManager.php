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


	/** @var DoorRepository */
	private $doorRepository;



	/**
	 * User manager constructor.
	 * @param NodeRepository $nodeRepository
	 * @param DoorRepository $doorRepository
	 * @param EntityManager $entityManager To propagate new node
	 */
	public function __construct(NodeRepository $nodeRepository, DoorRepository $doorRepository, EntityManager $entityManager)
	{
		$this->nodeRepository = $nodeRepository;
		$this->doorRepository = $doorRepository;
		$this->entityManager = $entityManager;
	}


	/**
	 * Add new node and register them doors
	 * @param string $title
	 * @param string $urlEndpoint
	 * @param bool $availableNfc
	 * @param array[doorId=>title] $doors
	 * @return Node
	 */
	public function addNode($title, $urlEndpoint, $availableNfc, array $doors = array())
	{
		$node = new Node();
		$node->setTitle($title);
		$node->regenerateApiKey($this);
		$node->setApiEndpointUrl($urlEndpoint);
		$node->setAvailabilityNfc($availableNfc);
		$this->entityManager->persist($node);

		foreach ($doors as $title) {
			$door = new Door($node, $title);
			$this->entityManager->persist($door);
		}

		$this->entityManager->flush();

		return $node;
	}


	/**
	 * Update node
	 * @param Node $node
	 * @param string $title
	 * @param string $endPointUrl
	 * @param bool $availableNfc
	 * @param bool $regenerateApi if is true, API key will be regenerated!
	 */
	public function updateNode(Node $node, $title, $endPointUrl, $availableNfc, $regenerateApi = FALSE)
	{
		$node->setTitle($title);
		$node->setApiEndpointUrl($endPointUrl);
		$node->setAvailabilityNfc($availableNfc);
		if ($regenerateApi) {
			$node->regenerateApiKey($this);
		}
		$this->entityManager->flush();
	}


	/**
	 * Add door to node
	 * @param Node $node
	 * @param string $title
	 * @param int $openingTime opening time in seconds
	 * @param array[pin, closed_zero, output] $gpioConfiguration
	 * @return Door
	 */
	public function addDoor(Node $node, $title, $openingTime, array $gpioConfiguration)
	{
		$door = new Door($node, $title);
		$door->setDefaultOpeningTime((int) $openingTime * 1000);
		$this->changeGpio($door, $gpioConfiguration);
		$this->entityManager->persist($door);
		$this->entityManager->flush();
		return $door;
	}


	/**
	 * Update door door id
	 * @param int $doorId
	 * @param string $title
	 * @param float $openingTime in seconds
	 * @param array[pin, closed_zero, output] $gpioConfiguration
	 * @return Door
	 * @throws DoorIdNotFoundException
	 */
	public function updateDoor($doorId, $title, $openingTime, $gpioConfiguration)
	{
		$door = $this->doorRepository->find($doorId); /** @var $door Door */
		if (!$door) {
			throw new DoorIdNotFoundException($doorId);
		}
		$door->setTitle($title);
		$door->setDefaultOpeningTime((int) $openingTime * 1000);
		$this->changeGpio($door, $gpioConfiguration);
		$this->entityManager->flush($door);
		return $door;
	}


	/**
	 * Remove doors
	 * @param int $doorId
	 * @throws DoorIdNotFoundException if door not found by id
	 */
	public function removeDoor($doorId)
	{
		$door = $this->doorRepository->find($doorId);
		if (!$door) {
			throw new DoorIdNotFoundException($doorId);
		}
		$this->entityManager->remove($door);
		$this->entityManager->flush();
	}


	/**
	 * Return unused GPIO pins
	 * @return array
	 */
	public function getFreeGpio(Node $node)
	{
		$gpioAllowed = array(0, 2, 3, 4, 5);
		$used = $this->doorRepository->findPairs(array(
			'node' => $node,
		), 'gpioPin');
		return array_values(array_diff($gpioAllowed, $used));
	}


	/**
	 * GPIO change configuration in entity
	 * @param Door $door
	 * @param array[pin, closed_zero, output] $gpioConfig
	 */
	private function changeGpio(Door $door, $gpioConfig)
	{
		if (count(array_diff_key(array("pin" => TRUE, "closed_zero" => TRUE, "output" => TRUE), $gpioConfig)) > 0) {
			throw new \InvalidArgumentException('Missing one of parameter');
		}
		$door->setGpio($gpioConfig['pin'], $gpioConfig['closed_zero'], $gpioConfig['output']);
	}


	/**
	 * Remove node with associated doors
	 * @param Node $node
	 */
	public function removeNode(Node $node)
	{
		foreach ($node->getDoors() as $door) {
			$this->entityManager->remove($door);
		}
		$this->entityManager->remove($node);
		$this->entityManager->flush();
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
