<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\LearningMaterialStatus;
use AppBundle\Entity\LearningMaterialStatusInterface;
use AppBundle\Service\DataimportFileLocator;

/**
 * Class LoadLearningMaterialStatusData
 */
class LoadLearningMaterialStatusData extends AbstractFixture
{
    public function __construct(DataimportFileLocator $dataimportFileLocator)
    {
        parent::__construct($dataimportFileLocator, 'learning_material_status');
    }

    /**
     * @return LearningMaterialStatusInterface
     *
     * @see AbstractInterface::createEntity()
     */
    protected function createEntity()
    {
        return  new LearningMaterialStatus();
    }

    /**
     * @param LearningMaterialStatusInterface $entity
     * @param array $data
     * @return LearningMaterialStatusInterface
     *
     * @see AbstractInterface::populateEntity
     */
    protected function populateEntity($entity, array $data)
    {
        // `learning_material_status_id`,`title`
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        return $entity;
    }
}
