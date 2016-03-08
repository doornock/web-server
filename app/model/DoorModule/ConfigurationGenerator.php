<?php

namespace Doornock\Model\DoorModule;

interface ConfigurationGenerator
{

	/**
	 * Generate configuration "file" as text output - etc. yaml
	 * @param Node $node
	 * @return string
	 */
	function generate(Node $node);

}
