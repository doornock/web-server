<?php

namespace Doornock\Model\DoorModule;

/**
 * Container to basic information
 */
class SiteInformation
{
	/** @var string */
	private $guid;

	/** @var string */
	private $title;

	/**
	 * SiteInformation constructor.
	 * @param string $guid
	 * @param string $title
	 */
	public function __construct($guid, $title)
	{
		$this->guid = $guid;
		$this->title = $title;
	}

	/**
	 * Return GUID of network in a sequence of hexadecimal digits separated into five groups
	 * @return string
	 */
	public function getGuid()
	{
		return $this->guid;
	}

	/**
	 * Name of site
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}



}