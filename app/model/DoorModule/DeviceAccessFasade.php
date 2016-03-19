<?php

namespace Doornock\Model\DoorModule;


use Doctrine\ORM\EntityManager;
use Doornock\Model\UserModule\User;
use Nette;


/**
 * Service layer communicates with devices with API key
 */
class DeviceAccessFasade
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
	 * @param Device $device
	 * @return Door[]
	 */
	public function findDoorWithAccess(Device $device)
	{
		return $this->accessManager->findDoorWithAccess($device->getOwner());
	}


	/**
	 * Open door
	 * @param Device $device
	 * @param string $doorId
	 * @return bool if successful, if no access return false
	 */
	public function openDoor(Device $device, $doorId)
	{
		$doors = $this->accessManager->findDoorWithAccess($device->getOwner());

		foreach ($doors as $door) { /** @var $door Door */
			if ((string)$door->getId() === $doorId) {
				return $this->opener->openDoor($door);
			}
		}

		return FALSE;
	}


}
