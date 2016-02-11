<?php

namespace Doornock\AdminModule\Components;

use Doornock\Model\DoorModule\Device;
use Doornock\Model\DoorModule\DeviceQuery;
use Doornock\Model\DoorModule\DeviceRepository;
use Doornock\Model\DoorModule\Door;
use Doornock\Model\DoorModule\DoorRepository;
use Doornock\Model\DoorModule\Node;
use Doornock\Model\UserModule\Roles;
use Doornock\Model\UserModule\User;
use Doornock\Model\UserModule\UserQuery;
use Nette;
use Nextras;

class DoorGridFactory extends Nette\Object
{

	/**
	 * @var DoorRepository
	 */
	private $doorRepository;


	public function __construct(DoorRepository $doorRepository)
	{
		$this->doorRepository = $doorRepository;
	}


	public function create(Node $node)
	{
		$grid = new Nextras\Datagrid\Datagrid;
		$grid->addColumn('id');
		$grid->addColumn('title', 'Title');
		$grid->addColumn('opening_time', 'Opening time in seconds');

		$grid->setColumnGetterCallback(function (Door $row, $column) {
			if ($column === 'id') {
				return $row->getId();
			} else if ($column === 'opening_time') {
				return round($row->getOpeningTime() / 1000, 2) . "s";
			} else if ($column === 'title') {
				return $row->getTitle();
			}
			return '?' . $column;
		});


		$grid->setDataSourceCallback(function ($filter, $order) use ($node) {
			return $this->doorRepository->findDoorByNode($node);
		});
		return $grid;
	}
}
