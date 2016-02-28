<?php

namespace Doornock\ApiModule\Presenters;

use Doornock\Model\DoorModule\SiteInformation;
use Doornock\Model\UserManager;
use Nette;


class SitePresenter extends BasePresenter
{

	/** @var SiteInformation @inject */
	public $siteInformation;

	public function actionKnockKnock()
	{
		$this->sendSuccess(array(
			'site' =>
				array(
					'guid' => $this->siteInformation->getGuid(),
					'title' => $this->siteInformation->getTitle(),
					'registration-allowed' => false
				)
		));
	}

}
