<?php

namespace Doornock\AdminModule\Presenters;


use Doornock\Model\DoorModule\NodeExecuteCommandException;
use Doornock\Model\DoorModule\SiteInformation;
use Doornock\Model\DoorModule\WebAccessManager;
use Tracy\Debugger;

class DashboardPresenter extends BasePresenter
{
	/** @var WebAccessManager @inject */
	public $webAccessManager;

	/** @var SiteInformation @inject */
	public $siteInformation;

	public function renderDefault()
	{
		$this->template->accessTo = $this->webAccessManager->findDoorWithAccess();
		$this->template->siteInfo = $this->siteInformation;
	}


	/**
	 * @secured
	 */
	public function handleOpenDoor($doorId)
	{
		try {
			if ($this->webAccessManager->openDoor($doorId)) {
				$this->flashMessage('Door was opened', 'success');
			} else {
				$this->flashMessage('Door not found');
			}
		} catch (NodeExecuteCommandException $e) {
			if (Debugger::$productionMode) {
				Debugger::log($e);
				$this->flashMessage('Door is not found or node does not work', 'danger');
			} else {
				throw $e;
			}
		}
		$this->redirect('this');
	}
}
