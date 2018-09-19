<?php

namespace Tests\App\DataFixtures\ORM;

use App\Entity\LearningMaterialStatusInterface;

/**
 * Class LoadLearningMaterialStatusDataTest
 */
class LoadLearningMaterialStatusDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'App\Entity\Manager\LearningMaterialStatusManager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'App\DataFixtures\ORM\LoadLearningMaterialStatusData',
        ];
    }

    /**
     * @covers \App\DataFixtures\ORM\LoadLearningMaterialStatusData::load
     */
    public function testLoad()
    {
        $this->runTestLoad('learning_material_status.csv');
    }

    /**
     * @param array $data
     * @param LearningMaterialStatusInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `learning_material_status_id`,`title`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getTitle());
    }
}
