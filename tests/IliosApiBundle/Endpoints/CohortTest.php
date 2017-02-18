<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * Cohort API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class CohortTest extends AbstractEndpointTest
{
    protected $testName =  'cohorts';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadCohortData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text],
            'programYear' => ['programYear', $this->getFaker()->text],
            'courses' => ['courses', [1]],
            'learnerGroups' => ['learnerGroups', [1]],
            'users' => ['users', [1]],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnliesToTest()
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 1]],
            'title' => [[0], ['title' => 'test']],
            'programYear' => [[0], ['programYear' => 'test']],
            'courses' => [[0], ['courses' => [1]]],
            'learnerGroups' => [[0], ['learnerGroups' => [1]]],
            'users' => [[0], ['users' => [1]]],
        ];
    }

}