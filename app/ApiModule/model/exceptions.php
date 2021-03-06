<?php

namespace Doornock\ApiModule\Model;

class AuthenticationException extends \Exception
{
	/** When token (e.g. content of auth header) to authentication is not valid */
	const REASON_INVALID_INPUT = 1;

	/** When token is not valid with data */
	const REASON_VERIFICATION = 2;

	/** When token is accepted object by identification not found (eg. device by device id) */
	const REASON_RELATED_OBJECT_NOT_FOUND = 3;

	/**
	 * Token has time problems, e.g. replay attack,
	 * when timestamp of input time and device time is out of accepted range
	 */
	const REASON_POSSIBLY_REPLAY_ATTACK = 4;

	/**
	 * AuthenticationException constructor.
	 */
	public function __construct($reason)
	{
		parent::__construct("Authentication failed", $reason);
	}


}
