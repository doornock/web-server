<?php

namespace Doornock\AdminModule\Presenters;


use Doornock\AdminModule\Components\UserGridFactory;
use Doornock\AdminModule\Forms\AddUserFormFactory;
use Nette\Http\IResponse;

class UserPresenter extends BasePresenter
{

	/** @var UserGridFactory @inject */
	public $gridFactory;


	/** @var AddUserFormFactory @inject */
	public $addUserFormFactory;

	protected function startup()
	{
		parent::startup();
		if (!$this->user->isAllowed('admin_users')) {
			$this->error('No access', IResponse::S403_FORBIDDEN);
		}
	}


	public function actionDefault()
	{
		$this['addForm']->onSuccess[] = function ($form, $values) {
			$this->flashMessage(sprintf("User '%s' was successfully added", $values->username), 'success');
			$this->redirect('this');
		};
	}


	public function createComponentGrid()
	{
		$grid = $this->gridFactory->create();
		$grid->addCellsTemplate(__DIR__ . '/templates/BaseGrid.latte');
		$grid->addCellsTemplate(__DIR__ . '/templates/User/UserGrid.latte');
		return $grid;
	}


	public function createComponentAddForm()
	{
		return $this->addUserFormFactory->create();
	}
}
