<?php

namespace Doornock\Model\DoorModule;

interface RestartNode
{

	/**
	 * Send request to restart node
	 * @param Node $node
	 * @return bool if request was successful
	 */
	function restartNode(Node $node);

}
