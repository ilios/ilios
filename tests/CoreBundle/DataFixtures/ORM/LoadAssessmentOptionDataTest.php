<?php

namespace Tests\CoreBundle\DataFixtures\ORM;

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
     * @covers Ilios\CoreBundle\DataFixtures\ORM\LoadAssessmentOptionData::load
     */
    public function testLoad()
    {
        $this->runTestLoad('assessment_option.csv');
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
}
