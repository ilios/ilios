<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\UserRole;
use AppBundle\Entity\UserRoleInterface;
use AppBundle\Service\DataimportFileLocator;

/**
 * Class LoadUserRoleData
 */
class LoadUserRoleData extends AbstractFixture
{
    public function __construct(DataimportFileLocator $dataimportFileLocator)
    {
        parent::__construct($dataimportFileLocator, 'user_role');
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
