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
		parent::__construct();
		$this->userManager = $userManager;
	}


	public function actionRegisterRandom()
	{
		$data = $this->userManager->registerRandomCredentials();
		$this->sendSuccess(array(
			'username' => $data['entity']->getUsername(),
			'password' => $data['password']
		));
	}

}
