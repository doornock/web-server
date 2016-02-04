<?php

namespace Doornock\ApiModule\Presenters;

use Doornock\Model\UserModule\UserManager;
use Nette;


class UserPresenter extends BasePresenter
{

	/** @var UserManager */
	public $userManager;


	public function actionRegisterRandom()
	{
		$data = $this->userManager->registerRandomCredentials();
		$this->sendResponse(new Nette\Application\Responses\JsonResponse(array(
			'status' => "OK",
			'data' => array(
				'username' => $data['entity']->getUsername(),
				'password' => $data['password']
			)
		)));
	}

}
