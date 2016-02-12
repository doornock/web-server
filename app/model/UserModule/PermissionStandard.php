<?php

namespace Doornock\Model\UserModule;


use Nette\Security\Permission;

class PermissionStandard extends Permission
{

	/**
	 * PermissionStandard constructor.
	 */
	public function __construct()
	{
		$this->addRole(Roles::USER);
		$this->addRole(Roles::ADMINISTRATOR, Roles::USER);

		$this->addResource('admin');
		$this->allow(Roles::ADMINISTRATOR);
		$this->addResource('admin_users', 'admin');
		$this->addResource('admin_nodes', 'admin');

		$this->allow(Roles::ADMINISTRATOR, 'admin_nodes');
		$this->allow(Roles::ADMINISTRATOR, 'admin_nodes', 'change_access');

		$this->allow(Roles::ADMINISTRATOR, 'admin_users');
		$this->allow(Roles::ADMINISTRATOR, 'admin_users', 'change_password_without_actual');
		$this->allow(Roles::ADMINISTRATOR, 'admin_users', 'change_role');
	}
}
