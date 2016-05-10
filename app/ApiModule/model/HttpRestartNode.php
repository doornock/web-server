<?php

namespace Doornock\ApiModule\Model;


use Doornock\Model\DoorModule\Node;
use Doornock\Model\DoorModule\NodeExecuteCommandException;
use Doornock\Model\DoorModule\RestartNode;
use Nette\Utils\JsonException;

class HttpRestartNode extends ApiSender implements RestartNode
{

	/**
	 * Send request to restart node
	 * @param Node $node
	 * @return bool if request was successful
	 * @throws NodeExecuteCommandException
	 */
	public function restartNode(Node $node)
	{
		return $this->sendToNode($node, 'restart', "");
	}

}

