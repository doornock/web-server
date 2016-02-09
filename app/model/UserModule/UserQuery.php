<?php

namespace Doornock\Model\UserModule;


use Kdyby;
use Kdyby\Doctrine\QueryObject;
use Kdyby\Persistence\Queryable;

class UserQuery extends QueryObject
{

	/**
	 * Part of username to search
	 * @var string
	 */
	private $searchByUsername;

	/**
	 * Filter by which role
	 * @var array
	 */
	private $roles = array();


	/**
	 * Order by which column
	 * @var array
	 */
	private $orderBy = array();


	/**
	 * Search by username
	 * @param string $username part of username
	 */
	public function searchByUsername($username)
	{
		$this->searchByUsername = $username;
	}


	/**
	 * Add role to filter
	 * @param string $role
	 */
	public function hasRole($role)
	{
		$this->roles[] = $role;
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


	protected function doCreateQuery(Queryable $repository)
	{
		return $this->createBaseQuery($repository)->select('u');
	}

	protected function doCreateCountQuery(Queryable $repository)
	{
		return $this->createBaseQuery($repository)->select('COUNT(u.id)');
	}


	/**
	 * Create base query
	 * @param Queryable $repository
	 * @return Kdyby\Doctrine\QueryBuilder
	 */
	private function createBaseQuery(Kdyby\Persistence\Queryable $repository)
	{
		$qb = $repository->createQueryBuilder()
			->from(User::class, 'u');

		if ($this->searchByUsername) {
			$qb->andWhere('u.username LIKE :searchUsername');
			$qb->setParameter('searchUsername', "%" . $this->searchByUsername . "%");
		}

		if (count($this->roles)) {
			$qb->andWhere('u.role IN (:roles)');
			$qb->setParameter('roles', $this->roles);
		}

		foreach ($this->orderBy as $column) {
			$qb->addOrderBy("u." . $column);
		}

		return $qb;
	}

}
