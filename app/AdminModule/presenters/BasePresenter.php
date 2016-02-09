<?php

namespace Doornock\AdminModule\Presenters;

use Nette;
use Doornock\Model;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
	protected function startup()
	{
		parent::startup();
		if (!$this->user->isLoggedIn() || $this->user->isInRole('blocked')) {
			$this->flashMessage('Your account is blocked');
			$this->user->logout(TRUE);
			$this->redirect(':Front:Sign:in');
		}
	}

}
