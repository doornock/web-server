<?php

namespace Doornock\ApiModule\Presenters;

use Nette\Application\UI\Presenter;
use Nette\Application\Responses\JsonResponse;

abstract class BasePresenter extends Presenter
{

	/**
	 * Sends success as response
	 * @param mixed $data data which is possible to represented as JSON
	 */
	public function sendSuccess($data = array())
	{
		$this->sendResponse(new JsonResponse(array(
			'status' => "OK",
			'data' => $data
		)));
	}


	/**
	 * Sends bad request to user
	 * @param int $httpCode see 4xx on https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
	 * @param string $message message to user what he do bad and how do it better
	 * @param int $specificCode specify problem to code for better code result
	 * @param mixed|null $data data which is possible to represented as JSON, and helps resolve problem
	 */
	public function sendRequestError($httpCode, $message, $specificCode = 0, $data = NULL)
	{
		$this->getHttpResponse()->setCode($httpCode);
		$this->sendResponse(new JsonResponse(array(
			'status' => "ERROR",
			'error' => array(
				'code' => $specificCode,
				'message' => $message
			),
			'data' => $data
		)));
	}


	/**
	 * Sends information about server internal error
	 * @param string|null $message optional message to specify error
	 */
	public function sendInternalError($message = null)
	{
		$this->getHttpResponse()->setCode(500);
		$this->sendResponse(new JsonResponse(array(
			'status' => "ERROR",
			'error' => array(
				'message' => $message ?: "Sorry, internal server error"
			),
		)));
	}

}