<?php

namespace Doornock\AdminModule\Presenters;



use Doornock\AdminModule\Components\NodeGridFactory;
use Doornock\AdminModule\Forms\AddNodeFormFactory;
use Doornock\Model\DoorModule\Node;
use Doornock\Model\DoorModule\NodeRepository;
use Nette\Application\UI\Form;
use Nette\Http\IResponse;

class NodePresenter extends BasePresenter
{

	/** @var NodeGridFactory @inject */
	public $gridFactory;


	/** @var AddNodeFormFactory @inject */
	public $addNodeFormFactory;



	protected function startup()
	{
		parent::startup();
		if (!$this->user->isAllowed('admin_nodes')) {
			$this->error('No access', IResponse::S403_FORBIDDEN);
		}
	}


	public function createComponentGrid()
	{
		$grid = $this->gridFactory->create();
		$grid->addCellsTemplate(__DIR__ . '/templates/BaseGrid.latte');
		$grid->addCellsTemplate(__DIR__ . '/templates/Node/NodeGrid.latte');
		return $grid;
	}


	public function createComponentAddForm()
	{
		return $this->addNodeFormFactory->create(function (Form $form, Node $node) {
			$this->flashMessage(sprintf("Node '%s' was successfully added", $node->getId()), 'success');
			$this->redirect('NodeDetail:', array('nodeId' => $node->getId()));
		});
	}
}
