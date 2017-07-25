<?php

namespace Tests\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\UserRoleInterface;

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
        return 'Ilios\CoreBundle\Entity\Manager\UserRoleManager';
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
     * @covers \Ilios\CoreBundle\DataFixtures\ORM\LoadUserRoleData::load
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
