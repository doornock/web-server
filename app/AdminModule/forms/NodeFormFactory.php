<?php

namespace Doornock\AdminModule\Forms;

use Doornock\Model\DoorModule\Node;
use Doornock\Model\DoorModule\NodeManager;
use Nette\Application\UI\Form;
use Nette\Object;


class NodeFormFactory extends Object
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
	 * @param int $node if you want update node
	 * @return Form
	 */
	public function create(callable $onSuccess = NULL, Node $node = NULL)
	{
		$form = new Form();
		$form->addProtection();

		$form->addHidden('node_id');

		$form->addText('title', 'Title')
			->addRule(Form::FILLED, 'Please fill title');

		$form->addText('endpoint_url', 'Node URL')
			->addRule(Form::URL, 'Node URL API is not valid (could be filled without http://)');

		/*
				$form->addCheckbox('nfc_available', 'Has NFC reader?')
					->setDefaultValue(TRUE);
		*/
		if ($node) {
			$form->addCheckbox('generate_api_key', 'Generate new API key?');
			$form->addSubmit('send', 'Update node');

			$form->setDefaults(array(
				'title' => $node->getTitle(),
				'endpoint_url' => $node->getApiEndpointUrl()
			));
		} else {
			$form->addSubmit('send', 'Add node');
		}
		$form->onSuccess[] = function (Form $form, $values) use ($onSuccess, $node) {
			if ($node !== NULL) {
				$this->nodeManager->updateNode($node, $values->title, $values->endpoint_url, $values->generate_api_key);
				if ($onSuccess !== NULL) {
					$onSuccess($form, $node);
				}
			} else {
				$node = $this->nodeManager->addNode($values->title, $values->endpoint_url, array());
				if ($onSuccess !== NULL) {
					$onSuccess($form, $node);
				}
			}
		};

		return $form;
	}

}
