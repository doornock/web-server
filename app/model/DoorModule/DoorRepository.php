<?php

namespace Doornock\Model\DoorModule;


use Kdyby\Doctrine\EntityDao;

class DoorRepository extends EntityDao
{
	public function findDoorByNode(Node $node)
	{
		return $this->findBy(array(
			'node' => $node
		));
	}
}
