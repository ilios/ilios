<?php

namespace Ilios\CoreBundle\Tests\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\Manager\AssessmentOptionManagerInterface;
use Ilios\CoreBundle\Entity\AssessmentOptionInterface;

/**
 * Class LoadAssessmentOptionDataTest
 * @package Ilios\CoreBundle\Tests\DataFixtures\ORM
 */
class LoadAssessmentOptionDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getDataFileName()
    {
        return 'assessment_option.csv';
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'ilioscore.assessmentoption.manager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'Ilios\CoreBundle\DataFixtures\ORM\LoadAssessmentOptionData',
        ];
    }

    /**
     * @param array $data
     * @param AssessmentOptionInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `assessment_option_id`,`name`
        $this->assertEquals($data[0], $entity->getId());
        $this->assertEquals($data[1], $entity->getName());
    }

    /**
     * @param array $data
     * @return AssessmentOptionInterface
     * @override
     */
    protected function getEntity(array $data)
    {
        /**
         * @var AssessmentOptionManagerInterface $em
         */
        $em = $this->em;
        return $em->findAssessmentOptionBy(['id' => $data[0]]);
    }
}
