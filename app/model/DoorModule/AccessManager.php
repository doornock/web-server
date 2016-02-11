<?php

namespace Doornock\Model\DoorModule;


use Doctrine\ORM\EntityManager;
use Doornock\Model\UserModule\User;
use Nette;


class AccessManager
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
	 * Find doors which has device access
	 * @return Door[]
	 * @throws ApiKeyNotFoundException if api key is not found
	 * @throws DeviceIsBlockedException when device is blocked and cannot do commands
	 */
	public function findDoorWithAccess(User $user)
	{
		$q = new AccessDoorQuery();
		$q->setUser($user->getOwner());

		return $this->doorRepository->fetch($q)->toArray();
	}


	/**
	 * Has user access?
	 * @param User $user
	 * @param Door $door
	 * @return bool
	 */
	public function hasAccess(User $user, Door $door)
	{
		$access = $this->entityManager->getRepository(UserAccess::class)->findOneBy(array(
			'user' => $user,
			'door' => $door
		)); /** @var $access UserAccess */
		return $access && $access->isAccess();
	}



	/**
	 * Allow user to access to door
	 * @param User $user
	 * @param Door $door
	 */
	public function allow(User $user, Door $door)
	{
		$door = new UserAccess($user, $door);
		$this->entityManager->persist($door);
		$this->entityManager->flush();
	}


	/**
	 * Decline user to access to door
	 * @todo inject (non-exist) repository
	 * @param User $user
	 * @param Door $door
	 */
	public function deny(User $user, Door $door)
	{
		$access = $this->entityManager->getRepository(UserAccess::class)->findOneBy(array(
			'user' => $user,
			'door' => $door
		));
		if ($access) {
			$this->entityManager->remove($access);
		}
		$this->entityManager->flush();
	}


}
