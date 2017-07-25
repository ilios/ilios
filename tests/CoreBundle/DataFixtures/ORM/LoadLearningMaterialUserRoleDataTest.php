<?php

namespace Tests\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\LearningMaterialUserRoleInterface;

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
        return 'Ilios\CoreBundle\Entity\Manager\LearningMaterialUserRoleManager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadLearningMaterialUserRoleData',
        ];
    }

    /**
     * @covers \Ilios\CoreBundle\DataFixtures\ORM\LoadLearningMaterialUserRoleData::load
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
