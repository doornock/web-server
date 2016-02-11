<?php

namespace Doornock\Model\DoorModule;


use Kdyby\Doctrine\EntityDao;

class NodeRepository extends EntityDao
{


	/**
	 * Find node by id
	 * @param string $id
	 * @return null|Node returns null if not found
	 */
	public function getById($id)
	{
		return $this->find($id);
	}

}
