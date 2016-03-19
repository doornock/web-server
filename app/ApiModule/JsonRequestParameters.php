<?php

namespace Doornock\ApiModule;


use Nette\Application\BadRequestException;
use Nette\Http\IRequest;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

class JsonRequestParameters
{
	/** @var String parsed data */
	private $data;

	/** @var callable */
	private $onMissingParam;

	/** @var IRequest */
	private $request;

	public function __construct(IRequest $request, callable $onMissingParam)
	{
		$this->request = $request;
		$this->onMissingParam = $onMissingParam;

		$body = $request->getRawBody();
		try {
			$this->data = Json::decode($body, Json::FORCE_ARRAY);
		} catch (JsonException $e) {
			throw new BadRequestException("Invalid JSON in content", 400);
		}
	}

	public function getData()
	{
		return $this->data;
	}

	public function getParam($key, $default = NULL)
	{
		return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
	}

	public function requireParam($key)
	{
		return array_key_exists($key, $this->data) ? $this->data[$key] : call_user_func($this->onMissingParam, $key);
	}

	public function requireParamString($key)
	{
		return array_key_exists($key, $this->data) && is_string($key) ? $this->data[$key] : call_user_func($this->onMissingParam, $key);
	}


}