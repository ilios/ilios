<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\LearningMaterialUserRole;
use AppBundle\Entity\LearningMaterialUserRoleInterface;
use AppBundle\Service\DataimportFileLocator;

/**
 * Class LoadLearningMaterialUserRoleData
 */
class LoadLearningMaterialUserRoleData extends AbstractFixture
{
    public function __construct(DataimportFileLocator $dataimportFileLocator)
    {
        parent::__construct($dataimportFileLocator, 'learning_material_user_role');
    }

    /**
     * @return LearningMaterialUserRoleInterface
     *
     * @see AbstractFixture::createEntity()
     */
    protected function createEntity()
    {
        return new LearningMaterialUserRole();
    }

    /**
     * @param LearningMaterialUserRoleInterface $entity
     * @param array $data
     * @return LearningMaterialUserRoleInterface
     *
     * @see AbstractFixture::populateEntity()
     */
    protected function populateEntity($entity, array $data)
    {
        // `learning_material_user_role_id`,`title`
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        return $entity;
    }
}
