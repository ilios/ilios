<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\LearningMaterialUserRole;

/**
 * Class LoadLearningMaterialUserRoleData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadLearningMaterialUserRoleData extends AbstractFixture
{
    public function __construct()
    {
        parent::__construct('learning_material_user_role');
    }

    /**
     * {@inheritdoc}
     */
    protected function createEntity(array $data)
    {
        // `learning_material_user_role_id`,`title`
        $entity = new LearningMaterialUserRole();
        $entity->setId($data[0]);
        $entity->setTitle($data[1]);
        return $entity;
    }
}
