<?php

namespace Doornock\AdminModule\Presenters;



use Doornock\AdminModule\Components\DoorGridFactory;
use Doornock\AdminModule\Forms\NodeFormFactory;
use Doornock\AdminModule\Forms\DoorFormFactory;
use Doornock\Model\DoorModule\DoorIdNotFoundException;
use Doornock\Model\DoorModule\DoorRepository;
use Doornock\Model\DoorModule\Node;
use Doornock\Model\DoorModule\NodeManager;
use Doornock\Model\DoorModule\NodeRepository;
use Nette\Forms\Form;
use Nette\Http\IResponse;

class NodeDetailPresenter extends BasePresenter
{

	use \Nextras\Application\UI\SecuredLinksPresenterTrait;

	/** @var DoorGridFactory @inject */
	public $gridFactory;

	/** @var NodeFormFactory @inject */
	public $nodeFormFactory;

	/** @var DoorFormFactory @inject */
	public $doorFormFactory;

	/** @var NodeRepository  */
	private $nodeRepository;

	/** @var NodeManager */
	private $nodeManager;

	/** @var DoorRepository */
	private $doorRepository;

	/** @var int @persistent */
	public $nodeId;

	/** @var Node */
	private $node;

	/**
	 * NodeDetailPresenter constructor.
	 * @param NodeRepository $nodeRepository
	 * @param NodeManager $nodeManager
	 * @param DoorRepository $doorRepository
	 */
	public function __construct(NodeRepository $nodeRepository, NodeManager $nodeManager, DoorRepository $doorRepository)
	{
		parent::__construct();
		$this->nodeRepository = $nodeRepository;
		$this->nodeManager = $nodeManager;
		$this->doorRepository = $doorRepository;
	}


	protected function startup()
	{
		parent::startup();
		if (!$this->user->isAllowed('admin_nodes')) {
			$this->error('No access', IResponse::S403_FORBIDDEN);
		}

		$this->node = $this->nodeRepository->getById($this->getParameter('nodeId'));
		if (!$this->node) {
			$this->flashMessage('Node not found', 'danger');
			$this->redirect('Node:');
		}
	}


	/**
	 * @param int $doorId
	 */
	public function actionEditDoor($doorId)
	{
		$door = $this->doorRepository->find($doorId);
		if ($door === NULL) {
			$this->flashMessage('Door not found', 'danger');
			$this->redirect('default');
		}
		$this['form'] = $this->doorFormFactory->create($this->node, function (Form $form) {
			$this->flashMessage('Updated');
			$form->setValues(array());
			$this->redirect('default');
		}, $door);
		$this->template->door = $door;
	}


	/**
	 * @secured
	 * @param int $doorId
	 */
	public function handleDeleteDoor($doorId)
	{
		try {
			$this->nodeManager->removeDoor($doorId);
			$this->flashMessage('Door ' . $doorId . ' was deleted', 'success');
			$this->redirect('default');
		} catch (DoorIdNotFoundException $e) {
			$this->flashMessage('Door not found', 'danger');
			$this->redirect('default');
		}
	}

	/**
	 * @secured
	 */
	public function handleDeleteNode()
	{
		$nodeId = $this->node->getId();
		$this->nodeManager->removeNode($this->node);
		$this->flashMessage('Node ' . $nodeId . ' was deleted', 'success');
		$this->redirect('Node:');
	}


	public function renderDefault()
	{
		$this->template->node = $this->node;
	}


	public function createComponentAddDoorForm()
	{
		return $this->doorFormFactory->create($this->node, function (Form $form) {
			$this->flashMessage('Updated');
			$form->setValues(array());
		});
	}


	public function createComponentEditNodeForm()
	{
		return $this->nodeFormFactory->create(function (Form $form) {
			$this->flashMessage('Updated');
		}, $this->node);
	}



	public function createComponentDoorGrid()
	{
		$grid = $this->gridFactory->create($this->node);
		$grid->addCellsTemplate(__DIR__ . '/templates/BaseGrid.latte');
		$grid->addCellsTemplate(__DIR__ . '/templates/NodeDetail/DoorGrid.latte');
		return $grid;
	}

}
