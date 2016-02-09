<?php

namespace Doornock\Model\DoorModule;


use Doctrine\ORM\EntityManager;
use Doornock\Model\UserModule\User;
use Nette;


/**
 * Service layer communicates with devices
 */
class DeviceAccessManager implements ApiKeyGenerator
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
	 * Register new user's device to system
	 * @param User $owner
	 * @param string $publicKey public RSA key encoded in base64
	 * @param string $description description
	 * @return Device
	 */
	public function addDeviceRSA(User $owner, $publicKey, $description)
	{
		$device = new Device($owner);
		$device->setDescription($description);
		$device->changeRSAKeys($publicKey);
		$device->changeApiKey($this); // apiKeyGenerator

		$this->entityManager->persist($device);
		$this->entityManager->flush();

		return $device;
	}


	/**
	 * Change RSA keys by API key to identify device
	 * @param string $apiKey device's API key
	 * @param string $publicKey public RSA key encoded in base64
	 * @param string|null $privateKey private RSA key encoded in base64
	 * @throws ApiKeyNotFoundException if api key is not found
	 */
	public function updateRSAKeyDeviceByApi($apiKey, $publicKey, $privateKey = NULL)
	{
		$device = $this->getDeviceByApiKey($apiKey);
		$device->changeRSAKeys($publicKey, $privateKey);

		$this->entityManager->flush();
	}


	/**
	 * Generate free API key for device
	 *
	 * @use only as ApiKeyGenerator
	 *
	 * @return string
	 */
	public function generateApiKey()
	{
		do {
			$apiKey = Nette\Utils\Random::generate(100);
			$exists = (bool) $this->deviceRepository->countBy(array(
				"apiKey" => $apiKey
			));
		} while ($exists);
		return $apiKey;
	}


	/**
	 * Find doors which has device access
	 * @return Door[]
	 * @throws ApiKeyNotFoundException if api key is not found
	 */
	public function findDoorWithAccess($apiKey)
	{
		$device = $this->getDeviceByApiKey($apiKey);

		$q = new AccessDoorQuery();
		$q->setUser($device->getOwner());

		return $this->doorRepository->fetch($q)->toArray();
	}


	/**
	 * Open door
	 * @param string $apiKey device api key
	 * @param string $doorId
	 * @throws @todo Exception about no access
	 */
	public function openDoor($apiKey, $doorId)
	{
		$device = $this->getDeviceByApiKey($apiKey);

		throw new Nette\NotImplementedException;
	}


	/**
	 * Find device by API key, or throws exception
	 * @param string $apiKey
	 * @return Device
	 * @throws ApiKeyNotFoundException
	 */
	private function getDeviceByApiKey($apiKey)
	{
		$device = $this->deviceRepository->findOneBy(array(
			"apiKey" => $apiKey
		)); /** @var $device Device */

		if (!$device) {
			throw new ApiKeyNotFoundException($apiKey);
		}

		return $device;
	}

}
