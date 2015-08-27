<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\LearningMaterialStatus;

/**
 * Class LoadLearningMaterialStatusData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadLearningMaterialStatusData extends AbstractFixture
{
    public function __construct()
    {
        parent::__construct('learning_material_status');
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntity(array $data)
    {
        // `learning_material_status_id`,`title`
        $entity = new LearningMaterialStatus();
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        return $entity;
    }
}
