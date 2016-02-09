<?php

namespace Doornock\AdminModule\Forms;

use Doornock\Model\UserModule\ChangeRoleOnSelfNotAllowedException;
use Doornock\Model\UserModule\MinimumCountAdministratorException;
use Doornock\Model\UserModule\Roles;
use Doornock\Model\UserModule\User;
use Doornock\Model\UserModule\UserManager;
use Nette\Application\UI\Form;
use Nette\Object;


class ChangeRoleFormFactory extends Object
{

	/**
	 * @var Roles
	 */
	private $roles;


	/** @var \Nette\Security\User */
	private $security;


	/** @var User */
	private $user;


	/** @var UserManager */
	private $userManager;

	/**
	 * ChangeRoleFormFactory constructor.
	 * @param UserManager $userManager
	 * @param \Nette\Security\User $security
	 * @param Roles $roles
	 */
	public function __construct(UserManager $userManager, \Nette\Security\User $security, Roles $roles)
	{
		$this->userManager = $userManager;
		$this->security = $security;
		$this->roles = $roles;
	}


	/**
	 * Which user will be changed
	 * @param User $user
	 */
	public function setUser($user)
	{
		$this->user = $user;
	}


	public function create()
	{
		$form = new Form();
		$form->addProtection();
		$form->addSelect('role', 'Role', $this->roles->findRolesWithTitle());
		$form->addSubmit('send', 'Change');
		$form->onSuccess[] = array($this, 'formSucceeded');
		return $form;
	}


	public function formSucceeded(Form $form, $values)
	{
		if (!$this->security->isAllowed('admin_users', 'change_role')) {
			$form->addError('No permission!');
		}
		try {
			$this->userManager->changeRole($this->user, $values->role, $this->security->getIdentity());
		} catch (MinimumCountAdministratorException $e) {
			$form->addError('You are last administrator');
		} catch (ChangeRoleOnSelfNotAllowedException $e) {
			$form->addError('You can not change role on yourself');
		}
	}

}
