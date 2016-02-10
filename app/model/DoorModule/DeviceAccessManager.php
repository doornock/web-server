<?php

namespace Doornock\Model\DoorModule;


use Doctrine\ORM\EntityManager;
use Doornock\Model\UserModule\User;
use Nette;


/**
 * Service layer communicates with devices with API key
 */
class DeviceAccessManager
{

	/** @var DeviceRepository */
	private $deviceRepository;

	/** @var DoorRepository */
	private $doorRepository;


	/** @var EntityManager */
	private $entityManager;


	/**
	 * User manager constructor.
	 * @param DeviceRepository $deviceRepository
	 * @param DoorRepository $doorRepository look up doors with access
	 * @param EntityManager $entityManager To propagate new device
	 */
	public function __construct(
		DeviceRepository $deviceRepository,
		DoorRepository $doorRepository,
		EntityManager $entityManager
	)
	{
		$this->deviceRepository = $deviceRepository;
		$this->doorRepository = $doorRepository;
		$this->entityManager = $entityManager;
	}


	/**
	 * Find doors which has device access
	 * @return Door[]
	 * @throws ApiKeyNotFoundException if api key is not found
	 * @throws DeviceIsBlockedException when device is blocked and cannot do commands
	 */
	public function findDoorWithAccess($apiKey)
	{
		$device = $this->getDevice($apiKey);

		$q = new AccessDoorQuery();
		$q->setUser($device->getOwner());

		return $this->doorRepository->fetch($q)->toArray();
	}


	/**
	 * Open door
	 * @param string $apiKey device api key
	 * @param string $doorId
	 * @todo implement it!
	 * @throws ApiKeyNotFoundException if api key is not found
	 * @throws DeviceIsBlockedException when device is blocked and cannot do commands
	 */
	public function openDoor($apiKey, $doorId)
	{
		$device = $this->getDevice($apiKey);

		throw new Nette\NotImplementedException;
	}


	/**
	 * Find device by API key, or throws exception
	 * @param string $apiKey
	 * @return Device
	 * @throws ApiKeyNotFoundException if not found
	 * @throws DeviceIsBlockedException when device is blocked and cannot do commands
	 */
	private function getDevice($apiKey)
	{
		$device = $this->deviceRepository->getDeviceByApiKey($apiKey);
		if ($device->isBlocked()) {
			throw new DeviceIsBlockedException($device);
		}
		return $device;
	}


}
