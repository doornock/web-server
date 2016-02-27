<?php

namespace Doornock\Model\DoorModule;

interface Opener
{

	/**
	 * Send command to open door
	 * @param Door $door
	 * @return bool
	 */
	function openDoor(Door $door);

}