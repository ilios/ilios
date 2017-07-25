<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\UserRole;
use Ilios\CoreBundle\Entity\UserRoleInterface;

/**
 * Class LoadUserRoleData
 */
class LoadUserRoleData extends AbstractFixture
{
    public function __construct()
    {
        parent::__construct('user_role');
    }

    /**
     * @return UserRoleInterface
     *
     * @see AbstractFixture::createEntity()
     */
    protected function createEntity()
    {
        return new UserRole();
    }

    /**
     * @param UserRoleInterface $entity
     * @param array $data
     * @return UserRoleInterface
     *
     * @see AbstractFixture::createEntity()
     */
    protected function populateEntity($entity, array $data)
    {
        // `user_role_id`,`title`
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        return $entity;
    }
}
