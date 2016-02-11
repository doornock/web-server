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

	/** @var AccessManager */
	private $accessManager;

	/**
	 * DeviceAccessManager constructor.
	 * @param DeviceRepository $deviceRepository
	 * @param AccessManager $accessManager
	 */
	public function __construct(DeviceRepository $deviceRepository, AccessManager $accessManager)
	{
		$this->deviceRepository = $deviceRepository;
		$this->accessManager = $accessManager;
	}


	/**
	 * Find doors which has device access
	 * @param string $apiKey
	 * @return Door[]
	 * @throws ApiKeyNotFoundException if api key is not found
	 * @throws DeviceIsBlockedException when device is blocked and cannot do commands
	 */
	public function findDoorWithAccess($apiKey)
	{
		$device = $this->getDevice($apiKey);
		return $this->accessManager->findDoorWithAccess($device->getOwner())->toArray();
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
