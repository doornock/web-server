<?php

namespace Doornock\Model;


use Nette;


class NodeFacade
{


	/** @var Nette\Database\Context */
	private $database;


	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}


}