<?php

namespace Doornock\AdminModule\Components;

use Doornock\Model\DoorModule\Door;
use Doornock\Model\DoorModule\Node;
use Doornock\Model\DoorModule\NodeQuery;
use Doornock\Model\DoorModule\NodeRepository;
use Nette;
use Nextras;

class NodeGridFactory extends Nette\Object
{

	/** @var NodeRepository */
	private $nodeRepository;



	public function __construct(NodeRepository $nodeRepository)
	{
		$this->nodeRepository = $nodeRepository;
	}


	public function create()
	{
		$grid = new Nextras\Datagrid\Datagrid;
		$grid->addColumn('id', 'Id')
			->enableSort();
		$grid->addColumn('title', 'Name');
		//$grid->addColumn('availableNfc', 'NFC reader');
		$grid->addColumn('doors', 'Doors');
		$grid->setFilterFormFactory(function () {
			$form = new Nette\Forms\Container;
			$form->addText('id', 'Id');
			$form->addText('title', 'Name');
			$form->addSelect('availableNfc', 'NFC reader', array(
				FALSE => 'Not available',
				TRUE => 'Available'
			))->setPrompt("-");
			$form->addText('doors', 'Doors');

			return $form;
		});

		$grid->setColumnGetterCallback(function (Node $row, $column) {
			if ($column === 'id') {
				return $row->getId();
			} else if ($column === 'title') {
				return $row->getTitle();
			} else if ($column === 'doors') {
				$doors = array();
				foreach ($row->getDoors() as $door) { /** @var $door Door */
					$doors[] = $door->getTitle();
				}
				return implode(', ', $doors);
			} else if ($column === 'availableNfc') {
				return $row->isAvailableNfc() ? 'Available' : 'No';
			}
			return "?" . $column;
		});

		$q = function ($filter, $order) {
			$q = new NodeQuery();
			if (isset($filter['title'])) {
				$q->searchByTitle($filter['title']);
			}
			if (isset($filter['doors'])) {
				$q->searchWithDoor($filter['doors']);
			}
			if (isset($filter['id'])) {
				$q->exactlyId($filter['id']);
			}
			if (isset($filter['availableNfc'])) {
				$q->hasNfcAvailable((bool) $filter['availableNfc']);
			}
			if ($order !== NULL && $order[0] === 'id') {
				$q->orderBy('id', strtoupper($order[1]) === 'ASC');
			}
			return $q;
		};

		$grid->setDataSourceCallback(function ($filter, $order, Nette\Utils\Paginator $paginator) use ($q) {
			return $this->nodeRepository->fetch($q($filter, $order))->applyPaginator($paginator);
		});
		$grid->setPagination(5, function ($filter, $order) use ($q) {
			return $this->nodeRepository->fetch($q($filter, $order))->getTotalCount();
		});
		return $grid;
	}
}
