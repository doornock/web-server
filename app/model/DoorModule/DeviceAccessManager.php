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

	/** @var Opener */
	private $opener;


	/**
	 * DeviceAccessManager constructor.
	 * @param DeviceRepository $deviceRepository
	 * @param AccessManager $accessManager
	 * @param Opener $opener to open doors
	 */
	public function __construct(DeviceRepository $deviceRepository, AccessManager $accessManager, Opener $opener)
	{
		$this->deviceRepository = $deviceRepository;
		$this->accessManager = $accessManager;
		$this->opener = $opener;
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
		return $this->accessManager->findDoorWithAccess($device->getOwner());
	}


	/**
	 * Open door
	 * @param string $apiKey device api key
	 * @param string $doorId
	 * @return bool if successful, if no access return false
	 * @throws ApiKeyNotFoundException if api key is not found
	 * @throws DeviceIsBlockedException when device is blocked and cannot do commands
	 */
	public function openDoor($apiKey, $doorId)
	{
		$device = $this->getDevice($apiKey);
		$doors = $this->accessManager->findDoorWithAccess($device->getOwner());

		foreach ($doors as $door) { /** @var $door Door */
			if ((string)$door->getId() === $doorId) {
				return $this->opener->openDoor($door);
			}
		}

		return FALSE;
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
