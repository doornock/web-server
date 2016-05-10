<?php

namespace Doornock\ApiModule\Model;


use Doornock\Model\DoorModule\Door;
use Doornock\Model\DoorModule\NodeExecuteCommandException;
use Doornock\Model\DoorModule\Opener;
use GuzzleHttp\Client;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Nette\Utils\Validators;

class HttpOpenDoor extends ApiSender implements Opener
{

	/**
	 * Open door on request
	 * @param Door $door
	 * @throws JsonException
	 * @return bool if request was successful
	 * @throws NodeExecuteCommandException
	 */
	function openDoor(Door $door)
	{
		$data = Json::encode(array(
			'door_id' => (string) $door->getId(),
			'opening_time' => $door->getOpeningTime()
		));

		return $this->sendToNode($door->getNode(), 'open-door', $data);
	}

}
