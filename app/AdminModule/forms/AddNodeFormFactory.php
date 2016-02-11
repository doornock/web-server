<?php

namespace Doornock\AdminModule\Forms;

use Doornock\Model\DoorModule\NodeManager;
use Nette\Application\UI\Form;
use Nette\Object;


class AddNodeFormFactory extends Object
{

	/**
	 * @var NodeManager
	 */
	private $nodeManager;

	/**
	 * AddNodeFormFactory constructor.
	 * @param NodeManager $nodeManager to register node
	 */
	public function __construct(NodeManager $nodeManager)
	{
		$this->nodeManager = $nodeManager;
	}


	/**
	 * @param callable $onSuccess callback(Form $form, Node $node)
	 * @return Form
	 */
	public function create(callable $onSuccess = NULL)
	{
		$form = new Form();
		$form->addProtection();

		$form->addText('title', 'Title')
			->addRule(Form::FILLED, 'Please fill title');

		$form->addCheckbox('nfc_available', 'Has NFC reader?')
			->setDefaultValue(TRUE);


		$form->addSubmit('send', 'Add node');
		$form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
			$node = $this->nodeManager->addNode($values->title, array(), $values->nfc_available);
			if ($onSuccess !== NULL) {
				$onSuccess($form, $node);
			}
		};

		return $form;
	}

}
