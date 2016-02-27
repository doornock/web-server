<?php

namespace Doornock\Model\DoorModule;


use Doctrine\ORM\EntityManager;
use Doornock\Model\UserModule\User;
use Nette;


/**
 * Service layer communicates with user over web
 */
class WebAccessManager
{

	/** @var Nette\Security\User */
	private $netteSecurity;

	/** @var AccessManager */
	private $accessManager;

	/** @var Opener */
	private $opener;


	/**
	 * DeviceAccessManager constructor.
	 * @param Nette\Security\User $netteSecurity
	 * @param AccessManager $accessManager
	 * @param Opener $opener to open doors
	 */
	public function __construct(Nette\Security\User $netteSecurity, AccessManager $accessManager, Opener $opener)
	{
		$this->netteSecurity = $netteSecurity;
		$this->accessManager = $accessManager;
		$this->opener = $opener;
	}


	/**
	 * Find doors which has use access
	 * @param User $asIdentity if you want control as another user or NULL if by logged user
	 * @return Door[]
	 * @throws AccessUnauthorizedException if user is not logged in
	 */
	public function findDoorWithAccess(User $asIdentity = NULL)
	{
		if ($asIdentity) {
			return $this->accessManager->findDoorWithAccess($asIdentity);
		}

		if (!$this->netteSecurity->isLoggedIn()) {
			throw new AccessUnauthorizedException(AccessUnauthorizedException::USER_NOT_FOUND);
		}
		return $this->accessManager->findDoorWithAccess($this->netteSecurity->getIdentity());
	}


	/**
	 * Open door
	 * @param string $doorId
	 * @param User $asIdentity if you want control as another user
	 * @return bool if successful, if no access return false
	 * @throws ApiKeyNotFoundException if api key is not found
	 * @throws DeviceIsBlockedException when device is blocked and cannot do commands
	 */
	public function openDoor($doorId, User $asIdentity = NULL)
	{
		$doors = $this->findDoorWithAccess($asIdentity);

		foreach ($doors as $door) { /** @var $door Door */
			if ((string)$door->getId() === $doorId) {
				return $this->opener->openDoor($door);
			}
		}

		return FALSE;
	}



}
