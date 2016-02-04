<?php

namespace Doornock\Model\DoorModule;


interface ApiKeyGenerator
{
	/**
	 * @return mixed
	 */
	function generateApiKey();
}