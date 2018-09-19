<?php

namespace Tests\App\DataFixtures\ORM;

use App\Entity\LearningMaterialUserRoleInterface;

/**
 * Class LoadLearningMaterialUserRoleDataTest
 */
class LoadLearningMaterialUserRoleDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'App\Entity\Manager\LearningMaterialUserRoleManager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'App\DataFixtures\ORM\LoadLearningMaterialUserRoleData',
        ];
    }

    /**
     * @covers \App\DataFixtures\ORM\LoadLearningMaterialUserRoleData::load
     */
    public function testLoad()
    {
        $this->runTestLoad('learning_material_user_role.csv');
    }

    /**
     * @param array $data
     * @param LearningMaterialUserRoleInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `learning_material_user_role_id`,`title`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getTitle());
    }
}
