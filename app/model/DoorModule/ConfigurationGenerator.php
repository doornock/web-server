<?php

namespace Doornock\Model\DoorModule;

interface ConfigurationGenerator
{

	/**
	 * Generate configuration "file" as text output - etc. yaml
	 * @param string $nodeId
	 * @return string
	 */
	function generate($nodeId);

}
