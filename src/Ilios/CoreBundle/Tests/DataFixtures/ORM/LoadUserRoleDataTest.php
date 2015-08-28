<?php

namespace Ilios\CoreBundle\Tests\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\Manager\UserRoleManagerInterface;
use Ilios\CoreBundle\Entity\UserRoleInterface;

/**
 * Class LoadUserRoleDataTest
 * @package Ilios\CoreBundle\Tests\DataFixtures\ORM
 */
class LoadUserRoleDataTest extends AbstractDataFixtureTest
{
    /**
     * @return string
     */
    public function getDataFileName()
    {
        return 'user_role.csv';
    }

    /**
     * @return string
     */
    public function getEntityManagerServiceKey()
    {
        return 'ilioscore.userrole.manager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadUserRoleData',
        ];
    }

    /**
     * @param array $data
     * @param UserRoleInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `user_role_id`,`title`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getTitle());

    }

    /**
     * @param array $data
     * @return UserRoleInterface
     * @override
     */
    protected function getEntity(array $data)
    {
        /**
         * @var UserRoleManagerInterface $em
         */
        $em = $this->em;
        return $em->findUserRoleBy(['id' => $data[0]]);
    }
}
