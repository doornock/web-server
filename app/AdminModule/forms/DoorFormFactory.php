<?php

namespace Doornock\AdminModule\Forms;

use Doornock\Model\DoorModule\Door;
use Doornock\Model\DoorModule\Node;
use Doornock\Model\DoorModule\NodeManager;
use Nette\Application\UI\Form;
use Nette\Object;

class DoorFormFactory extends Object
{
	/** @var NodeManager */
	private $nodeManager;


	public function __construct(NodeManager $nodeManager)
	{
		$this->nodeManager = $nodeManager;
	}


	/**
	 * @param Node $node
	 * @param callable(Form $form, Door $door) $onSuccess called after door is created
	 * @param Door $door door to edit
	 * @return Form
	 */
	public function create(Node $node, callable $onSuccess = NULL, Door $door = NULL)
	{
		$form = new Form();
		$form->addProtection();
		$form->addHidden('door_id');
		$form->addText('title', 'Title')
			->addRule(Form::FILLED, 'Please fill title');

		$form->addText('opening_time', 'Opening time in seconds (only number)')
			->addRule(Form::NUMERIC, 'Opening time must be only number (without "s", etc.)')
			->addRule(Form::RANGE, 'Opening time must be 1s to 30s', array(1, 30));

		$form->addSubmit('send', $door ? 'Update door' : 'Add door');

		if ($door) {
			$form->setDefaults(array(
				'door_id' => $door->getId(),
				'title' => $door->getTitle(),
				'opening_time' => $door->getOpeningTime() / 1000
			));
		}

		$form->onSuccess[] = function (Form $form, $values) use ($node, $onSuccess) {
			if ($values->door_id) {
				$door = $this->nodeManager->updateDoor($values->door_id, $values->title, $values->opening_time);
			} else {
				$door = $this->nodeManager->addDoor($node, $values->title, $values->opening_time);
			}
			if ($onSuccess !== NULL) {
				$onSuccess($form, $door);
			}
		};

		return $form;
	}
}