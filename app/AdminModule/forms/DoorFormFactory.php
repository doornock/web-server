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

		$freeGpio = $this->nodeManager->getFreeGpio($node);
		if ($door != NULL) $freeGpio[] = $door->getGpioPin();
		$freeGpio = array_combine($freeGpio, $freeGpio);

		$form->addSelect('gpio_pin', 'GPIO pin', $freeGpio)
			->setRequired("GPIO pin must be selected, if select is empty, you cannot create next door");
		$form->addCheckbox('gpio_closed_zero', 'Doors is closed when GPIO is on logic zero');
		$form->addCheckbox('gpio_output', 'GPIO is output')->setDefaultValue(TRUE);

		$form->addText('opening_time', 'Opening time in seconds (only number)')
			->addRule(Form::NUMERIC, 'Opening time must be only number (without "s", etc.)')
			->addRule(Form::RANGE, 'Opening time must be 1s to 30s', array(1, 30));

		$form->addSubmit('send', $door ? 'Update door' : 'Add door');

		if ($door) {
			$form->setDefaults(array(
				'door_id' => $door->getId(),
				'title' => $door->getTitle(),
				'opening_time' => $door->getOpeningTime() / 1000,
				'gpio_pin' => $door->getGpioPin(),
				'gpio_closed_zero' => $door->isGpioClosedOnZero(),
				'gpio_output' => $door->isGpioOutput()
			));
		}

		$form->onSuccess[] = function (Form $form, $values) use ($node, $onSuccess) {
			$gpioConfig = array(
				'pin' => $values->gpio_pin,
				'closed_zero' => $values->gpio_closed_zero,
				'output' => $values->gpio_output
			);

			if ($values->door_id) {
				$door = $this->nodeManager->updateDoor($values->door_id, $values->title, $values->opening_time, $gpioConfig);
			} else {
				$door = $this->nodeManager->addDoor($node, $values->title, $values->opening_time, $gpioConfig);
			}
			if ($onSuccess !== NULL) {
				$onSuccess($form, $door);
			}
		};

		return $form;
	}
}