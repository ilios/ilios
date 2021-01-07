<?php

declare(strict_types=1);

namespace App\Tests\DataFixtures\ORM;

use App\Entity\AssessmentOptionInterface;
use App\Repository\AssessmentOptionRepository;

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
        return AssessmentOptionRepository::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
            'App\DataFixtures\ORM\LoadAssessmentOptionData',
        ];
    }

    /**
     * @covers \App\DataFixtures\ORM\LoadAssessmentOptionData::load
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
