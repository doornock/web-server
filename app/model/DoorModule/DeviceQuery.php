<?php

namespace Doornock\Model\DoorModule;


use Doornock\Model\UserModule\User;
use Kdyby\Doctrine\QueryObject;
use Kdyby\Persistence\Queryable;

class DeviceQuery extends QueryObject
{

	/** @var User */
	private $owner;

	/** @var string */
	private $searchByOwnerUsername;

	/** @var string */
	private $searchByDescription;

	/** @var string */
	private $id;

	/** @var $blocking */
	private $blocking;

	/**
	 * Order by which column
	 * @var array
	 */
	private $orderBy = array();



	/**
	 * Define owner of devices
	 * @param User $owner
	 */
	public function setOwner(User $owner)
	{
		$this->owner = $owner;
	}


	/**
	 * Search by part of username
	 * @param string $username
	 */
	public function searchByOwnerUsername($username)
	{
		$this->searchByOwnerUsername = $username;
	}



	/**
	 * Search by part of description
	 * @param string $text
	 */
	public function searchByDescription($text)
	{
		$this->searchByDescription = $text;
	}


	/**
	 * Return by id
	 * @param string $id
	 */
	public function exactlyId($id)
	{
		$this->id = $id;
	}


	/**
	 * Set devices by blocking
	 * @param bool|null $isBlocked bool for filter only, or null to reset (all)
	 */
	public function byBlocking($isBlocked)
	{
		if (!is_bool($isBlocked) && $isBlocked !== NULL) {
			throw new \InvalidArgumentException('byBlocking() parameter must be bool or null to reset, ' . gettype($isBlocked) . " given");
		}
		$this->blocking = $isBlocked;
	}


	/**
	 * Order by which column (ordered by order call)
	 * @param string $column
	 * @param bool $asc sorting ascending (true) or descending (false)
	 */
	public function orderBy($column, $asc = TRUE)
	{
		$this->orderBy[$column] = $asc;
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
		$qb = $q->createQueryBuilder()->from(Device::class, 'd');
		if ($this->owner !== NULL) {
			$qb->andWhere('d.owner = :owner');
			$qb->setParameter('owner', $this->owner);
		} else if ($this->searchByOwnerUsername) {
			$qb->andWhere('d.owner.username LIKE :searchByOwnerUsername');
			$qb->setParameter('searchByOwnerUsername', "%" . $this->searchByOwnerUsername . "%");
		}

		if ($this->searchByDescription) {
			$qb->andWhere('d.description LIKE :searchByDescription');
			$qb->setParameter('searchByDescription', "%" . $this->searchByDescription . "%");
		}

		if ($this->id !== NULL) {
			$qb->andWhere('d.id = :id');
			$qb->setParameter('id', $this->id);
		}
		if ($this->blocking !== NULL) {
			$qb->andWhere('d.blocked = :blocked');
			$qb->setParameter('blocked', $this->blocking);
		}

		foreach ($this->orderBy as $key => $asc) {
			$qb->orderBy($key, $asc ? 'ASC' : 'DESC');
		}

		return $qb;
	}

}
