<?php

namespace Doornock\ApiModule;

use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

class Router extends RouteList
{

	/**
	 * Router constructor.
	 */
	public function __construct()
	{
		parent::__construct("Api");

		$this[] = new Route("v1/device/<action>", "Device:");
		$this[] = new Route("v1/site/<action>", "Site:");
		$this[] = new Route("v1/user/<action>", "User:");
	}

}