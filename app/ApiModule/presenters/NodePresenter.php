<?php

namespace Doornock\ApiModule\Presenters;

use Doornock\Model\DoorModule\AccessManager;
use Doornock\Model\DoorModule\Device;
use Doornock\Model\DoorModule\DeviceAccessFasade;
use Doornock\Model\DoorModule\DeviceRepository;
use Doornock\Model\DoorModule\NodeRepository;

class NodePresenter extends BasePresenter
{

	/** @var AccessManager @inject */
	public $deviceAccessManager;

	/** @var DeviceRepository */
	public $deviceRepository;

	/** @var NodeRepository */
	public $nodeRepository;


	/**
	 * NodePresenter constructor.
	 * @param DeviceRepository $deviceRepository
	 * @param NodeRepository $nodeRepository
	 */
	public function __construct(DeviceRepository $deviceRepository, NodeRepository $nodeRepository)
	{
		parent::__construct();
		$this->deviceRepository = $deviceRepository;
		$this->nodeRepository = $nodeRepository;
	}


	public function actionDevicePermission($device_id, $node_id = NULL)
	{
		if ($device_id === NULL) {
			$this->sendRequestError(400, 'Missing device_id parameter');
		}

		if ($node_id !== NULL) {
			$node = $this->nodeRepository->getById($node_id);
			if (!$node) {
				$this->sendRequestError(404, 'Node by node_id not found');
			}
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
		), isset($node) ? $node->getApiKey() : NULL);
	}
}

