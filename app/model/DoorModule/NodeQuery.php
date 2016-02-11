<?php

namespace Doornock\Model\DoorModule;


use Kdyby\Doctrine\QueryObject;
use Kdyby\Persistence\Queryable;

class NodeQuery extends QueryObject
{

	/** @var string */
	private $searchWithDoor;

	/** @var string */
	private $searchByTitle;

	/** @var string */
	private $id;

	/** @var bool|null */
	private $nfcAvailable;

	/**
	 * Order by which column
	 * @var array
	 */
	private $orderBy = array();


	/**
	 * Search by part of doors title or ID
	 * @param string $doorTitle
	 */
	public function searchWithDoor($doorTitle)
	{
		$this->searchWithDoor = $doorTitle;
	}


	/**
	 * Search by part of title
	 * @param string $text
	 */
	public function searchByTitle($text)
	{
		$this->searchByTitle = $text;
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
	 * Only device with/without nfc, or null for reset
	 * @param bool|null $available bool for filter only, or null to reset (all)
	 */
	public function hasNfcAvailable($available)
	{
		if (!is_bool($available) && $available !== NULL) {
			throw new \InvalidArgumentException('hasNfcAvailable() parameter must be bool or null to reset, ' . gettype($available) . " given");
		}
		$this->nfcAvailable = $available;
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
		return $this->createBaseQuery($repository)->select('n');
	}


	/**
	 * Create query for pagination and etc.
	 */
	protected function doCreateCountQuery(Queryable $repository)
	{
		return $this->createBaseQuery($repository)->select('COUNT(n.id)');
	}


	/**
	 * Base query for doCreateQuery and doCreateCountQuery
	 * @param Queryable $q
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	private function createBaseQuery(Queryable $q)
	{
		$qb = $q->createQueryBuilder()->from(Node::class, 'n');

		if ($this->searchWithDoor) {

			$qb->andWhere($qb->expr()->in(
				'n',
				$q->createQueryBuilder()
					->select('IDENTITY(d.node)')
					->from(Door::class, 'd')
					->where('d.title LIKE :searchWithDoor')
					->orWhere('d.id = :searchWithDoor')
					->getDQL()
			));
			$qb->setParameter('searchWithDoor', "%" . $this->searchWithDoor . "%");
		}

		if ($this->searchByTitle) {
			$qb->andWhere('n.title LIKE :searchByTitle');
			$qb->setParameter('searchByTitle', "%" . $this->searchByTitle . "%");
		}

		if ($this->id !== NULL) {
			$qb->andWhere('n.id = :id');
			$qb->setParameter('id', $this->id);
		}

		if ($this->nfcAvailable !== NULL) {
			$qb->andWhere('n.availableNfc = :nfcAvailable');
			$qb->setParameter('nfcAvailable', $this->nfcAvailable);
		}

		foreach ($this->orderBy as $key => $asc) {
			$qb->orderBy($key, $asc ? 'ASC' : 'DESC');
		}

		return $qb;
	}
}
