<?php

namespace Tests\AppBundle\DataFixtures\ORM;

use AppBundle\Entity\AssessmentOptionInterface;

/**
 * Class LoadAssessmentOptionDataTest
 */
class LoadAssessmentOptionDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'AppBundle\Entity\Manager\AssessmentOptionManager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'AppBundle\DataFixtures\ORM\LoadAssessmentOptionData',
        ];
    }

    /**
     * @covers \AppBundle\DataFixtures\ORM\LoadAssessmentOptionData::load
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
