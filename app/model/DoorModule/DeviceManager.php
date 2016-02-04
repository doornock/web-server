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
		$device = $this->deviceRepository->findOneBy(array(
			"apiKey" => $apiKey
		)); /** @var $device Device */

		if (!$device) {
			throw new ApiKeyNotFoundException($apiKey);
		}

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


}