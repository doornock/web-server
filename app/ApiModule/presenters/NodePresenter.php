<?php

namespace Doornock\ApiModule\Presenters;

use Doornock\Model\DoorModule\AccessManager;
use Doornock\Model\DoorModule\Device;
use Doornock\Model\DoorModule\DeviceAccessManager;
use Doornock\Model\DoorModule\DeviceRepository;

class NodePresenter extends BasePresenter
{

	/** @var AccessManager @inject */
	public $deviceAccessManager;

	/** @var DeviceRepository */
	public $deviceRepository;

	/**
	 * NodePresenter constructor.
	 * @param DeviceRepository $deviceRepository
	 */
	public function __construct(DeviceRepository $deviceRepository)
	{
		parent::__construct();
		$this->deviceRepository = $deviceRepository;
	}


	public function actionDevicePermission($device_id)
	{
		if ($device_id === NULL) {
			$this->sendRequestError(400, 'Missing device_id parameter');
		}

		$device = $this->deviceRepository->find($device_id); /** @var $device Device */
		if ($device === NULL) {
			$this->sendRequestError(404, 'Device not found');
		}

		$accessTo = $this->deviceAccessManager->findDoorWithAccess($device->getOwner());

		$doorsId = array();
		foreach ($accessTo as $door) {
			$doorsId[] = array(
				'id' => (string) $door->getId(),
				'opening_time' => $door->getOpeningTime(),
			);
		}

		$this->sendSuccess(array(
			'public_key' => $device->getPublicKey(),
			'door_with_access' => $doorsId
		));
	}
}

