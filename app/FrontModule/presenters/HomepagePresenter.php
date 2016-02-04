<?php

namespace Doornock\FrontModule\Presenters;

use Nette;


class HomepagePresenter extends BasePresenter
{

	public function renderDefault()
	{
		$this->template->anyVariable = 'any value';
	}

}
