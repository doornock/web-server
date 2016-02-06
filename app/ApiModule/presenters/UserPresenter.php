<?php

namespace Doornock\ApiModule\Presenters;

use Doornock\Model\UserModule\UserManager;
use Nette;


class UserPresenter extends BasePresenter
{

	/** @var UserManager */
	private $userManager;

	/**
	 * UserPresenter constructor.
	 * @param UserManager $userManager
	 */
	public function __construct(UserManager $userManager)
	{
		$this->userManager = $userManager;
	}


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
