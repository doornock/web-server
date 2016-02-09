<?php

namespace Doornock\Model\UserModule;

use Kdyby\Doctrine\EntityDao;
use Kdyby\Doctrine\EntityRepository;

class UserRepository extends EntityDao
{

	/**
	 * Find user by username, otherwise return null
	 * @param string $username
	 * @return User|null
	 */
	public function getByUsername($username)
	{
		return $this->findOneBy(array(
			'username' => $username
		));
	}

	/**
	 * Find user by id, otherwise return null
	 * @param int $id
	 * @return User|null
	 */
	public function getById($id)
	{
		return $this->findOneBy(array(
			'id' => $id
		));
	}
}
