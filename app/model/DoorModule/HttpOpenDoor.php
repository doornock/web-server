<?php

namespace Doornock\Model\DoorModule;


use GuzzleHttp\Client;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Nette\Utils\Validators;
use Tracy\Debugger;

class HttpOpenDoor implements Opener
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
		$node = $door->getNode();
		if (!Validators::isUrl($node->getApiEndpointUrl())) {
			throw new NodeExecuteCommandException('Node is not configured: Missing url, or invalid url');
		}
		$url = rtrim($node->getApiEndpointUrl(), '/') . '/open-door';

		$data = Json::encode(array(
			'door_id' => (string) $door->getId(),
			'opening_time' => $door->getOpeningTime()
		));

		return $this->send(
			$url,
			$data,
			$this->hmacHeader($node->getApiKey(), $data)
		);
	}


	/**
	 * Sends request and evaluate success
	 * @param string $url
	 * @param string $body
	 * @param string $authHeader
	 * @return bool
	 * @throws NodeExecuteCommandException if there is error
	 */
	private function send($url, $body, $authHeader)
	{
		$client = new Client();
		try {
			$response = $client->request('POST', $url, [
				'body' => $body,
				'headers' => array(
					'X-API-Auth-V1' => $authHeader
				)
			]);
			if ($response->getStatusCode() === 200) {
				$body = Json::decode($response->getBody());
				return $body->status === 'ok';
			}
			return FALSE;
		} catch (\Exception $e) {
			throw new NodeExecuteCommandException($e->getMessage(), $e->getCode(), $e);
		}
	}


	/**
	 * Generates HMAC auth
	 * @param string $apiKey secret
	 * @param string $data
	 * @return string
	 */
	private function hmacHeader($apiKey, $data)
	{
		$time = time();
		return $time . ' ' . hash_hmac('sha256', $time . "|" . $data, $apiKey);
	}

}
