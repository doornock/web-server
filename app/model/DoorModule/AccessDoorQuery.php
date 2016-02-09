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

	/**
	 * Define user
	 * @param User $user
	 */
	public function setUser(User $user)
	{
		$this->user = $user;
	}


	/**
	 * Create query
	 */
	protected function doCreateQuery(Queryable $repository)
	{
		return $this->createBaseQuery($repository)->select('d');
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

		return $q->createQueryBuilder()
			->from(Door::class, 'd')
			->leftJoin(UserAccess::class, 'ua')
			->where('ua.enable = :enable')
			->where('ua.user = :user')
			->set('enable', TRUE)
			->set('user', $this->user);
	}

}