<?php

namespace Doornock\ApiModule\Presenters;

use Doornock\ApiModule\Model\ApiAuthenticator;
use Doornock\ApiModule\Model\AuthenticationException;
use Doornock\Model\DoorModule\AccessManager;
use Doornock\Model\DoorModule\Device;
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

	/** @var ApiAuthenticator */
	private $nodeAuthenticator;


	/**
	 * NodePresenter constructor.
	 * @param DeviceRepository $deviceRepository
	 * @param NodeRepository $nodeRepository
	 * @param ApiAuthenticator $apiAuthenticator
	 */
	public function __construct(
		DeviceRepository $deviceRepository,
		NodeRepository $nodeRepository,
		ApiAuthenticator $apiAuthenticator
	)
	{
		parent::__construct();
		$this->deviceRepository = $deviceRepository;
		$this->nodeRepository = $nodeRepository;
		$this->nodeAuthenticator = $apiAuthenticator;
	}


	public function actionDevicePermission($device_id)
	{
		if ($device_id === NULL) {
			$this->sendRequestError(400, 'Missing device_id parameter');
		}

		try {
			$node = $this->nodeAuthenticator->authenticateNode($this->getHttpRequest());

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
			), $node->getApiKey());
		} catch (AuthenticationException $e) {
			$this->sendRequestError(401, "Authentication failed", $e->getCode());
		}
	}
}

