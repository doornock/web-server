<?php

namespace Doornock\ApiModule\Model;


use Doornock\Model\DoorModule\Door;
use Doornock\Model\DoorModule\Node;
use Doornock\Model\DoorModule\NodeExecuteCommandException;
use Doornock\Model\DoorModule\Opener;
use GuzzleHttp\Client;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Nette\Utils\Validators;

abstract class ApiSender
{

	/**
	 * Send request to node
	 * @param Node $node
	 * @param string $path
	 * @param string $data
	 * @return bool
	 * @throws NodeExecuteCommandException
	 */
	protected function sendToNode(Node $node, $path, $data)
	{
		if (!Validators::isUrl($node->getApiEndpointUrl())) {
			throw new NodeExecuteCommandException('Node is not configured: Missing url, or invalid url');
		}
		$url = rtrim($node->getApiEndpointUrl(), '/') . '/' . $path;

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
				return $body->status === 'OK';
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
