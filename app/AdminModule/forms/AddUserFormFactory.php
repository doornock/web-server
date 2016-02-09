<?php

namespace Doornock\AdminModule\Forms;

use Doornock\Model\UserModule\User;
use Doornock\Model\UserModule\UserManager;
use Doornock\Model\UserModule\UsernameAlreadyRegisteredException;
use Doornock\Model\UserModule\UsernameNotFoundException;
use Nette\Application\UI\Form;
use Nette\Object;


class AddUserFormFactory extends Object
{

	/**
	 * @var UserManager
	 */
	private $userManager;

	/**
	 * AddUserFormFactory constructor.
	 * @param UserManager $userManager to register user
	 */
	public function __construct(UserManager $userManager)
	{
		$this->userManager = $userManager;
	}


	public function create()
	{
		$form = new Form();
		$form->addText('username', 'Username')
			->addRule(Form::FILLED, 'Please fill username');

		$form->addText('password', 'New password')
			->addRule(Form::FILLED, 'Please fill password')
			->addRule(Form::MIN_LENGTH, 'Password should be long %d characters at least', 5)
			->addRule(~Form::PATTERN, 'Password should have one special characters (e.g. !%.,/)', '^[a-zA-Z0-9]*$');

		$form->addText('password_repeat', 'Repeat new password')
			->addRule(Form::EQUAL, 'New password and repeated is not same', $form['password']);

		$form->addSubmit('send', 'Add user');
		$form->onSuccess[] = array($this, 'formSucceeded');

		return $form;
	}


	public function formSucceeded(Form $form, $values)
	{
		try {
			$user = $this->userManager->register(
				$values->username,
				$values->password
			);

		} catch (UsernameAlreadyRegisteredException $e) {
			$form->addError($e->getMessage());
		}
	}

}
