<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * InstructorGroup API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class InstructorGroupTest extends AbstractEndpointTest
{
    protected $testName =  'instructorgroup';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadInstructorGroupData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text],
            'school' => ['school', $this->getFaker()->text],
            'learnerGroups' => ['learnerGroups', [1]],
            'ilmSessions' => ['ilmSessions', [1]],
            'users' => ['users', [1]],
            'offerings' => ['offerings', [1]],
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
            'school' => [[0], ['school' => 'test']],
            'learnerGroups' => [[0], ['learnerGroups' => [1]]],
            'ilmSessions' => [[0], ['ilmSessions' => [1]]],
            'users' => [[0], ['users' => [1]]],
            'offerings' => [[0], ['offerings' => [1]]],
        ];
    }

}