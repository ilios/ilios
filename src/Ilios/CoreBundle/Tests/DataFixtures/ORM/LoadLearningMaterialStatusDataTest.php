<?php

namespace Ilios\CoreBundle\Tests\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\Manager\LearningMaterialStatusManagerInterface;
use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;

/**
 * Class LoadLearningMaterialStatusDataTest
 * @package Ilios\CoreBundle\Tests\DataFixtures\ORM
 */
class LoadLearningMaterialStatusDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getDataFileName()
    {
        return 'learning_material_status.csv';
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'ilioscore.learningmaterialstatus.manager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadLearningMaterialStatusData',
        ];
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

    /**
     * @param array $data
     * @return LearningMaterialStatusInterface
     * @override
     */
    protected function getEntity(array $data)
    {
        /**
         * @var LearningMaterialStatusManagerInterface $em
         */
        $em = $this->em;
        return $em->findLearningMaterialStatusBy(['id' => $data[0]]);
    }
}
