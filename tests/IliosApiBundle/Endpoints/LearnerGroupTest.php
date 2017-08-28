<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * LearnerGroup API endpoint Test.
 * @group api_2
 */
class LearnerGroupTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'learnerGroups';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadLearnerGroupData',
            'Tests\CoreBundle\Fixture\LoadLearnerGroupData',
            'Tests\CoreBundle\Fixture\LoadCohortData',
            'Tests\CoreBundle\Fixture\LoadIlmSessionData',
            'Tests\CoreBundle\Fixture\LoadOfferingData',
            'Tests\CoreBundle\Fixture\LoadUserData',
            'Tests\CoreBundle\Fixture\LoadVocabularyData',
            'Tests\CoreBundle\Fixture\LoadTermData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text(60)],
            'location' => ['location', $this->getFaker()->text(100)],
            'cohort' => ['cohort', 3],
            'parent' => ['parent', 2],
            'removeParent' => ['parent', null],
            'children' => ['children', [1], $skipped = true],
            'ilmSessions' => ['ilmSessions', [2]],
            'offerings' => ['offerings', [2]],
            'instructorGroups' => ['instructorGroups', [1, 2]],
            'users' => ['users', [1]],
            'instructors' => ['instructors', [1, 2]],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest()
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
            'ids' => [[1, 2], ['id' => [2, 3]]],
            'title' => [[2], ['title' => 'third learner group']],
            'location' => [[3], ['location' => 'fourth location']],
            'cohort' => [[1], ['cohort' => 2]],
            'parent' => [[3], ['parent' => 1]],
            'noParent' => [[0, 1, 2, 4], ['parent' => 'null']],
            'children' => [[0], ['children' => [4]], $skipped = true],
            'ilmSessions' => [[0, 2], ['ilmSessions' => [1]], $skipped = true],
            'offerings' => [[1, 4], ['offerings' => [2]], $skipped = true],
            'instructorGroups' => [[0], ['instructorGroups' => [1]], $skipped = true],
            'users' => [[0, 4], ['users' => [5]], $skipped = true],
            'instructors' => [[0, 2], ['instructors' => [1]], $skipped = true],
            'cohorts' => [[1], ['cohorts' => [2]]],
        ];
    }

    public function testPostLearnerGroupIlmSession()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'learnerGroups', 'ilmSessions');
    }

    public function testPostLearnerGroupOfferings()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'learnerGroups', 'offerings');
    }
}
