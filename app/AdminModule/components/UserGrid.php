<?php

namespace Doornock\AdminModule\Components;

use Doornock\Model\UserModule\Roles;
use Doornock\Model\UserModule\User;
use Doornock\Model\UserModule\UserQuery;
use Doornock\Model\UserModule\UserRepository;
use Nette;
use Nextras;

class UserGridFactory extends Nette\Object
{

	/** @var Roles */
	private $roles;

	/** @var UserRepository */
	private $userRepository;

	/**
	 * UserGridFactory constructor.
	 * @param UserRepository $userRepository
	 * @param Roles $roles for filter role
	 */
	public function __construct(UserRepository $userRepository, Roles $roles)
	{
		$this->userRepository = $userRepository;
		$this->roles = $roles;
	}


	public function create()
	{
		$grid = new Nextras\Datagrid\Datagrid;
		$grid->addColumn('username')
			->enableSort();
		$grid->addColumn('roles');
		$grid->setFilterFormFactory(function () {
			$form = new Nette\Forms\Container;
			$form->addText('username', 'Username');
			$form->addSelect('roles', 'Role', $this->roles->findRolesWithTitle())->setPrompt("-");

			return $form;
		});

		$grid->setColumnGetterCallback(function (User $row, $column) {
			if ($column === 'username') {
				return $row->getUsername();
			} else if ($column === 'roles') {
				return implode(',', $row->getRoles());
			}
		});

		$q = function ($filter, $order) {
			$q = new UserQuery();
			if (isset($filter['username'])) {
				$q->searchByUsername($filter['username']);
			}
			if (isset($filter['roles'])) {
				$q->hasRole($filter['roles']);
			}
			if ($order !== NULL && $order[0] === 'username') {
				$q->orderBy('username', strtoupper($order[1]) === 'ASC');
			}
			return $q;
		};

		$grid->setDataSourceCallback(function ($filter, $order, Nette\Utils\Paginator $paginator) use ($q) {
			return $this->userRepository->fetch($q($filter, $order))->applyPaginator($paginator);
		});
		$grid->setPagination(5, function ($filter, $order) use ($q) {
			return $this->userRepository->fetch($q($filter, $order))->getTotalCount();
		});
		return $grid;
	}
}
