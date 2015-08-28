<?php

namespace Ilios\CoreBundle\Tests\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\Manager\LearningMaterialUserRoleManagerInterface;
use Ilios\CoreBundle\Entity\LearningMaterialUserRoleInterface;

/**
 * Class LoadLearningMaterialUserRoleDataTest
 * @package Ilios\CoreBundle\Tests\DataFixtures\ORM
 */
class LoadLearningMaterialUserRoleDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getDataFileName()
    {
        return 'learning_material_user_role.csv';
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'ilioscore.learningmaterialuserrole.manager';
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
     * @param array $data
     * @param LearningMaterialUserRoleInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `learning_material_user_role_id`,`title`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getTitle());
    }

    /**
     * @param array $data
     * @return LearningMaterialUserRoleInterface
     * @override
     */
    protected function getEntity(array $data)
    {
        /**
         * @var LearningMaterialUserRoleManagerInterface $em
         */
        $em = $this->em;
        return $em->findLearningMaterialUserRoleBy(['id' => $data[0]]);
    }
}
