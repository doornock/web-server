<?php

namespace Doornock\ApiModule\Presenters;

use Doornock\Model\UserManager;
use Nette;


class SitePresenter extends BasePresenter
{

	public function actionKnockKnock()
	{
		$this->sendSuccess(array(
			'site' =>
				array(
					'guid' => 'f5bdf871-20a7-4bc0-865b-e7a1a56b6a43',
					'title' => 'DOORNOCK HQ-dev',
					'registration-allowed' => true
				)
		));
	}

}
