<?php

namespace Doornock\AdminModule\Forms;

use Doornock\Model\UserModule\User;
use Doornock\Model\UserModule\UserManager;
use Doornock\Model\UserModule\UsernameNotFoundException;
use Nette\Application\UI\Form;
use Nette\Object;


class ChangePasswordFormFactory extends Object
{

	/**
	 * @var UserManager
	 */
	private $userManager;


	/**
	 * @var User|string username
	 */
	private $user;


	/**
	 * @var bool want to know old password
	 */
	private $checkPassword = TRUE;


	/**
	 * ChangePasswordFormFactory constructor.
	 * @param UserManager $userManager to change password
	 */
	public function __construct(UserManager $userManager)
	{
		$this->userManager = $userManager;
	}


	/**
	 * Set which users password will be changed
	 * @param User|string $user string as username
	 */
	public function setUser($user)
	{
		$this->user = $user;
	}


	/**
	 * Disable control if password is same as actual, it's good for administrators
	 */
	public function disableCheckActualPassword()
	{
		$this->checkPassword = FALSE;
	}


	public function create()
	{
		$form = new Form();
		$actual = $form->addText('password_actual', 'Actual password')
			->addRule(Form::FILLED, 'Please fill password');

		if ($this->checkPassword) {
			$actual->setDisabled();
		}

		$form->addText('password_new', 'New password')
			->addRule(Form::FILLED, 'Please fill password')
			->addRule(Form::MIN_LENGTH, 'Password should be long %d characters at least', 5)
			->addRule(~Form::PATTERN, 'Password should have one special characters (e.g. !%.,/)', '^[a-zA-Z0-9]*$');

		$form->addText('password_repeat', 'Repeat new password')
			->addRule(Form::EQUAL, 'New password and repeated is not same', $form['password_new']);

		$form->addSubmit('send', 'Change password');
		$form->onSuccess[] = array($this, 'formSucceeded');

		return $form;
	}


	public function formSucceeded(Form $form, $values)
	{
		try {
			$result = $this->userManager->changePassword(
				$this->user->getUsername(),
				$values->password_new,
				$this->checkPassword ? $values->password_actual : NULL
			);

			if (!$result) {
				$form->addError('Actual password is not same as you typed');
			}

		} catch (UsernameNotFoundException $e) {
			$form->addError($e->getMessage());
		}
	}

}
