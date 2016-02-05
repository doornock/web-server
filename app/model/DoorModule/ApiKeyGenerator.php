<?php

namespace Doornock\Model\DoorModule;


interface ApiKeyGenerator
{
	/**
	 * Generates API key
	 * @return string
	 */
	function generateApiKey();
}
