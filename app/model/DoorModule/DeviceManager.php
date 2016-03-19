<?php

namespace Doornock\Model\DoorModule;


use Doctrine\ORM\EntityManager;
use Doornock\Model\UserModule\User;
use Nette;



class DeviceManager implements ApiKeyGenerator
{

	/** @var DeviceRepository */
	private $deviceRepository;


	/** @var EntityManager */
	private $entityManager;


	/**
	 * User manager constructor.
	 * @param DeviceRepository $deviceRepository
	 * @param EntityManager $entityManager To propagate new device
	 */
	public function __construct(
		DeviceRepository $deviceRepository,
		EntityManager $entityManager
	)
	{
		$this->deviceRepository = $deviceRepository;
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
	 * @param string $deviceId device's id
	 * @param string $publicKey public RSA key encoded in base64
	 * @param string|null $privateKey private RSA key encoded in base64
	 * @throws ApiKeyNotFoundException if api key is not found
	 */
	public function updateRSAKeyDeviceByApi($deviceId, $publicKey, $privateKey = NULL)
	{
		$device = $this->deviceRepository->getDeviceById($deviceId);
		$device->changeRSAKeys($publicKey, $privateKey);

		$this->entityManager->flush();
	}


	/**
	 * Block device by id
	 * @param string $deviceId
	 * @param User $mustBeUser device could be blocked only if owner is matched
	 * @return boolean
	 * @throws DeviceNotFoundException if device is not found
	 */
	public function blockDeviceById($deviceId, User $mustBeUser = NULL)
	{
		$device = $this->deviceRepository->getDeviceById($deviceId);
		if ($mustBeUser !== NULL && $device->getOwner()->getId() === $mustBeUser->getId()) {
			return FALSE;
		}
		$device->block();
		$this->entityManager->flush($device);
		return TRUE;
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
			$apiKey = Nette\Utils\Random::generate(50, "a-zA-Z0-9-,");
			$exists = (bool) $this->deviceRepository->countBy(array(
				"apiKey" => $apiKey
			));
		} while ($exists);
		return $apiKey;
	}

}
