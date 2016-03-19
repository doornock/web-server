<?php

namespace Doornock\Model\DoorModule;


use Kdyby\Doctrine\EntityDao;

class DeviceRepository extends EntityDao
{

	/**
	 * Find device by id, or throws exception
	 * @param int $deviceId
	 * @return Device
	 * @throws DeviceNotFoundException
	 */
	public function getDeviceById($deviceId)
	{
		$device = $this->find($deviceId); /** @var $device Device */
		if ($device === NULL) {
			throw new DeviceNotFoundException($deviceId);
		}
		return $device;
	}

	/**
	 * Find device by API key, or throws exception
	 * @param string $apiKey
	 * @return Device
	 * @throws ApiKeyNotFoundException if not found
	 * @throws DeviceIsBlockedException when device is blocked and cannot do commands
	 */
	public function getDeviceByApiKey($apiKey)
	{
		$device = $this->findOneBy(array(
			"apiKey" => $apiKey
		)); /** @var $device Device */

		if (!$device) {
			throw new ApiKeyNotFoundException($apiKey);
		}

		return $device;
	}
}
