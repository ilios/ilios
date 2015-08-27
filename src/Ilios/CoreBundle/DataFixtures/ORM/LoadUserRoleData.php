<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\UserRole;

/**
 * Class LoadUserRoleData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadUserRoleData extends AbstractFixture
{
    public function __construct()
    {
        parent::__construct('user_role');
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntity(array $data)
    {
        // `user_role_id`,`title`
        $entity = new UserRole();
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        return $entity;
    }
}
