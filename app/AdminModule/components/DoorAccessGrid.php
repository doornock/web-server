<?php

namespace Doornock\AdminModule\Components;

use Doornock\Model\DoorModule\AccessDoorQuery;
use Doornock\Model\DoorModule\Door;
use Doornock\Model\DoorModule\DoorRepository;
use Doornock\Model\DoorModule\Node;
use Doornock\Model\UserModule\User;
use Nette;
use Nextras;

class DoorAccessGridFactory extends Nette\Object
{

	/**
	 * @var DoorRepository
	 */
	private $doorRepository;

	public function __construct(DoorRepository $doorRepository)
	{
		$this->doorRepository = $doorRepository;
	}


	public function create(User $user)
	{
		$grid = new Nextras\Datagrid\Datagrid;
		$grid->addColumn('id');
		$grid->addColumn('title', 'Title');

		$grid->setColumnGetterCallback(function (Door $row, $column) {
			if ($column === 'id') {
				return $row->getId();
			} else if ($column === 'title') {
				return $row->getTitle();
			}
			return '?' . $column;
		});

		$grid->setColumnGetterCallback(function (Node $row, $column) {
			if ($column === 'id') {
				return $row->getId();
			} else if ($column === 'title') {
				return $row->getTitle();
			}
			return "?" . $column;
		});

		$q = function ($filter, $order) use ($user) {
			$q = new AccessDoorQuery();
			$q->setUser($user);
			return $q;
		};

		$grid->setDataSourceCallback(function ($filter, $order, Nette\Utils\Paginator $paginator) use ($q) {
			return $this->doorRepository->fetch($q($filter, $order))->applyPaginator($paginator);
		});
		$grid->setPagination(100, function ($filter, $order) use ($q) {
			return $this->doorRepository->fetch($q($filter, $order))->getTotalCount();
		});
		return $grid;
	}
}
