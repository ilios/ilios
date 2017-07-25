<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\LearningMaterialUserRole;
use Ilios\CoreBundle\Entity\LearningMaterialUserRoleInterface;

/**
 * Class LoadLearningMaterialUserRoleData
 */
class LoadLearningMaterialUserRoleData extends AbstractFixture
{
    public function __construct()
    {
        parent::__construct('learning_material_user_role');
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
