<?php

namespace Doornock\AdminModule\Components;

use Doornock\Model\DoorModule\AccessDoorQuery;
use Doornock\Model\DoorModule\Door;
use Doornock\Model\DoorModule\DoorRepository;
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
		$grid->addColumn('access', 'Access');

		$grid->setColumnGetterCallback(function (array $row, $column) {
			if ($column === 'id') {
				return $row[0]->getId();
			} else if ($column === 'title') {
				return $row[0]->getTitle();
			} else if ($column === 'access') {
				return $row['access'] ? "Yes" : "No";
			}
			return '?' . $column;
		});

		$grid->setFilterFormFactory(function () {
			$form = new Nette\Forms\Container;
			$form->addText('title');

			$form->addSelect('access', '', array(
				FALSE => 'Without access',
				TRUE => 'Has access'
			))->setPrompt('All');
			return $form;
		});

		$q = function ($filter, $order) use ($user) {
			$q = new AccessDoorQuery();
			$q->setUser($user);

			if (isset($filter['title'])) {
				$q->searchByTitle($filter['title']);
			}

			if (isset($filter['access'])) {
				$q->byAccess((bool) $filter['access']);
			}

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
