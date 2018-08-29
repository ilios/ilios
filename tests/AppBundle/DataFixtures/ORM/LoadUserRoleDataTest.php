<?php

namespace Tests\AppBundle\DataFixtures\ORM;

use AppBundle\Entity\UserRoleInterface;

/**
 * Class LoadUserRoleDataTest
 */
class LoadUserRoleDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'AppBundle\Entity\Manager\UserRoleManager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'AppBundle\DataFixtures\ORM\LoadUserRoleData',
        ];
    }

    /**
     * @covers \AppBundle\DataFixtures\ORM\LoadUserRoleData::load
     */
    public function testLoad()
    {
        $this->runTestLoad('user_role.csv');
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
}
