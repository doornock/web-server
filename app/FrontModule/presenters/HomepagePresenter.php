<?php

namespace Doornock\FrontModule\Presenters;

use Nette;


class HomepagePresenter extends BasePresenter
{

	public function actionDefault()
	{
		if ($this->user->isLoggedIn()) {
			$this->redirect(':Admin:Dashboard:');
		} else {
			$this->redirect('Sign:in');
		}
	}

}
