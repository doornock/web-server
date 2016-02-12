<?php

namespace Doornock\Model\DoorModule;


use Doornock\Model\UserModule\User;
use Kdyby;
use Kdyby\Doctrine\QueryObject;
use Kdyby\Persistence\Queryable;


/**
 * Query object to find doors which has user access
 */
class AccessDoorQuery extends QueryObject
{

	/** @var User */
	private $user;

	/** @var string */
	private $searchByTitle;

	/** @var bool */
	private $byAccess;


	/**
	 * Define user
	 * @param User $user
	 */
	public function setUser(User $user)
	{
		$this->user = $user;
	}


	/**
	 * Search by part of title
	 * @param string $title
	 */
	public function searchByTitle($title)
	{
		$this->searchByTitle = $title;
	}


	/**
	 * Filter by access
	 * @param bool|null $access bool for filter only, or null to reset (all)
	 */
	public function byAccess($access)
	{

		if (!is_bool($access) && $access !== NULL) {
			throw new \InvalidArgumentException('byBlocking() parameter must be bool or null to reset, ' . gettype($access) . " given");
		}
		$this->byAccess = $access;
	}



	/**
	 * Create query
	 */
	protected function doCreateQuery(Queryable $repository)
	{
		return $this->createBaseQuery($repository)->select('d, ua.access');
	}


	/**
	 * Create query for pagination and etc.
	 */
	protected function doCreateCountQuery(Queryable $repository)
	{
		return $this->createBaseQuery($repository)->select('COUNT(d.id)');
	}


	/**
	 * Base query for doCreateQuery and doCreateCountQuery
	 * @param Queryable $q
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	private function createBaseQuery(Queryable $q)
	{
		if ($this->user === NULL) {
			throw new \LogicException(__CLASS__ . " has no set User by ->setUser(User \$user)");
		}

		$qb = $q->createQueryBuilder()
			->from(Door::class, 'd')
			->leftJoin(UserAccess::class, 'ua', 'WITH', 'd = ua.door')
			->leftJoin(User::class, 'u', 'WITH', 'u = ua.user AND u = :user')
			->setParameter('user', $this->user);

		if ($this->searchByTitle) {
			$qb->andWhere('d.title LIKE :searchByTitle')
				->setParameter('searchByTitle', '%' . $this->searchByTitle . '%');
		}

		if ($this->byAccess === TRUE) {
			$qb->andWhere('ua.access = true');
		} else if ($this->byAccess === FALSE) {
			$qb->andWhere('ua.access = false OR ua.access IS NULL');
		}

		return $qb;
	}

}
