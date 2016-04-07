<?php

namespace Doornock\ApiModule;

use Nette;


/**
 * JSON response used mainly for AJAX requests.
 */
class JsonApiResponse implements Nette\Application\IResponse
{
	/** @var array|\stdClass */
	private $payload;

	/** @var string */
	private $secretKey;


	/**
	 * @param  array|\stdClass  payload
	 * @param  string    Sign key
	 */
	public function __construct($payload, $secretKey = NULL)
	{
		if (!is_array($payload) && !is_object($payload)) {
			throw new Nette\InvalidArgumentException(sprintf('Payload must be array or object class, %s given.', gettype($payload)));
		}
		$this->payload = $payload;
		$this->secretKey = $secretKey;
	}


	/**
	 * Sends response to output.
	 * @return void
	 */
	public function send(Nette\Http\IRequest $httpRequest, Nette\Http\IResponse $httpResponse)
	{
		$httpResponse->setContentType('application/json');
		$httpResponse->setExpiration(FALSE);
		$data = Nette\Utils\Json::encode($this->payload);

		if ($this->secretKey !== NULL) {
			$time = time();
			$httpResponse->addHeader(
				"X-API-Sign-V1",
				hash_hmac('sha256', $httpRequest->getHeader("X-API-Auth-V1") . "|" . $data, $this->secretKey)
			);
		}
		echo $data;
	}

}
