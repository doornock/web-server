<?php

namespace Doornock\ApiModule\Presenters;

use Doornock\ApiModule\Model\ApiAuthenticator;
use Doornock\ApiModule\Model\AuthenticationException;
use Doornock\Model\DoorModule\AccessManager;
use Doornock\Model\DoorModule\Device;
use Doornock\Model\DoorModule\DeviceRepository;
use Doornock\Model\DoorModule\Door;
use Doornock\Model\DoorModule\NodeRepository;
use Doornock\Model\DoorModule\SiteInformation;

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

	/** @var SiteInformation */
	private $siteInformation;


	/**
	 * NodePresenter constructor.
	 * @param DeviceRepository $deviceRepository
	 * @param NodeRepository $nodeRepository
	 * @param ApiAuthenticator $apiAuthenticator
	 * @param SiteInformation $siteInformation
	 */
	public function __construct(
		DeviceRepository $deviceRepository,
		NodeRepository $nodeRepository,
		ApiAuthenticator $apiAuthenticator,
		SiteInformation $siteInformation
	)
	{
		parent::__construct();
		$this->deviceRepository = $deviceRepository;
		$this->nodeRepository = $nodeRepository;
		$this->nodeAuthenticator = $apiAuthenticator;
		$this->siteInformation = $siteInformation;
	}


	public function actionLogInNode()
	{

		try {
			$node = $this->nodeAuthenticator->authenticateNode($this->getHttpRequest());

			$doors = array();
			foreach ($node->getDoors() as $door) { /** @var $door Door */
				$doors[] = array(
					'id' => (string) $door->getId(),
					'type' => 'gpio',
					'gpio' => $door->getGpioPin(),
					'closeIsZero' => $door->isGpioClosedOnZero(),
					'gpioOutput' => $door->isGpioOutput()
				);
			}

			$array = array(
				'site' => array(
					'guid' => $this->siteInformation->getGuid(),
					'title' => $this->siteInformation->getTitle()
				),
				'doors' => $doors
			);

			if ($node->isAvailableNfc()) {
				$array['nfc']['aid'] = 'F0394148148111';
			}

			$this->sendSuccess($array, $node->getApiKey());
		} catch (AuthenticationException $e) {
			$this->sendRequestError(401, "Authentication failed", $e->getCode());
		}
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

