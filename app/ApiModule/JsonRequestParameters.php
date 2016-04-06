<?php

namespace Doornock\ApiModule;


use Nette\Application\BadRequestException;
use Nette\Http\IRequest;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

class JsonRequestParameters
{
	/** @var array JSON decoded data */
	private $data;

	/** @var callable */
	private $onMissingParam;

	/** @var IRequest */
	private $request;


	/**
	 * JsonRequestParameters constructor.
	 * @param IRequest $request
	 * @param callable $onMissingParam is called when require parameter missing
	 * @throws BadRequestException when request content is not valid json
	 */
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

	/**
	 * Json decoded data
	 * @return mixed
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Alternative to getData()[$key] with default param, use only if you except json object on input
	 * @param string $key
	 * @param mixed|null $default
	 * @return mixed
	 * @throws BadRequestException is called when json input was not JSON object
	 */
	public function getParam($key, $default = NULL)
	{
		if (!is_array($this->data)) {
			throw new BadRequestException("Json has invalid format, root has to be object", 400);
		}
		return array_key_exists($key, $this->data) ? $this->data[$key] : $default;
	}

	/**
	 * Same as {@link getParam}, but instead default parameter,
	 * is called missing callback which was defined in constructor
	 */
	public function requireParam($key)
	{
		if (!is_array($this->data)) {
			throw new BadRequestException("Json has invalid format, root has to be object", 400);
		}
		return array_key_exists($key, $this->data)
			? $this->data[$key] : call_user_func($this->onMissingParam, $this, $key);
	}

	/**
	 * Same as {@link requireParam} but value of parameter must be string
	 */
	public function requireParamString($key)
	{
		if (!is_array($this->data)) {
			throw new BadRequestException("Json has invalid format, root has to be object", 400);
		}
		return array_key_exists($key, $this->data) && is_string($key)
			? $this->data[$key] : call_user_func($this->onMissingParam, $this, $key);
	}


}
