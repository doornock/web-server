<?php

namespace Doornock\AdminModule\Components;

use Doornock\Model\DoorModule\Device;
use Doornock\Model\DoorModule\DeviceQuery;
use Doornock\Model\DoorModule\DeviceRepository;
use Doornock\Model\UserModule\Roles;
use Doornock\Model\UserModule\User;
use Doornock\Model\UserModule\UserQuery;
use Nette;
use Nextras;

class DeviceGridFactory extends Nette\Object
{

	/** @var DeviceRepository */
	private $deviceRepository;

	/**
	 * DeviceGridFactory constructor.
	 * @param DeviceRepository $deviceRepository
	 */
	public function __construct(DeviceRepository $deviceRepository)
	{
		$this->deviceRepository = $deviceRepository;
	}


	public function create(User $owner = NULL)
	{
		$grid = new Nextras\Datagrid\Datagrid;
		$grid->addColumn('id')
			->enableSort();
		if ($owner === NULL) {
			$grid->addColumn('owner')
				->enableSort();
		}
		$grid->addColumn('description');
		$grid->addColumn('blocked');


		$grid->setFilterFormFactory(function () use ($owner) {
			$form = new Nette\Forms\Container;
			$form->addText('id', 'Id');
			if ($owner !== NULL) {
				$form->addText('owner', 'Owner\'s username');
			}
			$form->addText('description', 'Description');
			$form->addSelect('blocked', 'Blocking', array(
				FALSE => 'Usable',
				TRUE => 'Only blocked'
			))->setPrompt('All');

			return $form;
		});

		$grid->setColumnGetterCallback(function (Device $row, $column) {
			if ($column === 'id') {
				return $row->getId();
			} else if ($column === 'owner') {
				return $row->getOwner()->getUsername();
			} else if ($column === 'description') {
				return $row->getDescription();
			} else if ($column === 'blocked') {
				return $row->isBlocked() ? 'Blocked' : 'Usable';
			}
			return '?' . $column;
		});

		$q = function ($filter, $order) use ($owner) {
			$q = new DeviceQuery();
			if (isset($filter['id'])) {
				$q->exactlyId($filter['id']);
			}

			if ($owner !== NULL) {
				$q->setOwner($owner);
			} else if (isset($filter['owner'])) {
				$q->searchByOwnerUsername($filter['owner']);
			}


			if (isset($filter['blocked'])) {
				$q->byBlocking((bool) $filter['blocked']);
			}


			if (isset($filter['description'])) {
				$q->searchByOwnerDescription($filter['description']);
			}

			if ($order !== NULL && $order[0] === 'id') {
				$q->orderBy('id', strtoupper($order[1]) === 'ASC');
			}

			if ($order !== NULL && $order[0] === 'owner') {
				$q->orderBy('owner.username', strtoupper($order[1]) === 'ASC');
			}
			return $q;
		};

		$grid->setDataSourceCallback(function ($filter, $order, Nette\Utils\Paginator $paginator) use ($q) {
			return $this->deviceRepository->fetch($q($filter, $order))->applyPaginator($paginator);
		});
		$grid->setPagination(5, function ($filter, $order) use ($q) {
			return $this->deviceRepository->fetch($q($filter, $order))->getTotalCount();
		});
		return $grid;
	}
}
