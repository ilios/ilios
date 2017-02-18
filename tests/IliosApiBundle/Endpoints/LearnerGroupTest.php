<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * LearnerGroup API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class LearnerGroupTest extends AbstractEndpointTest
{
    protected $testName =  'learnergroups';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadLearnerGroupData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text],
            'location' => ['location', $this->getFaker()->text],
            'cohort' => ['cohort', $this->getFaker()->text],
            'parent' => ['parent', $this->getFaker()->text],
            'children' => ['children', [1]],
            'ilmSessions' => ['ilmSessions', [1]],
            'offerings' => ['offerings', [1]],
            'instructorGroups' => ['instructorGroups', [1]],
            'users' => ['users', [1]],
            'instructors' => ['instructors', [1]],
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
            'location' => [[0], ['location' => 'test']],
            'cohort' => [[0], ['cohort' => 'test']],
            'parent' => [[0], ['parent' => 'test']],
            'children' => [[0], ['children' => [1]]],
            'ilmSessions' => [[0], ['ilmSessions' => [1]]],
            'offerings' => [[0], ['offerings' => [1]]],
            'instructorGroups' => [[0], ['instructorGroups' => [1]]],
            'users' => [[0], ['users' => [1]]],
            'instructors' => [[0], ['instructors' => [1]]],
        ];
    }

}