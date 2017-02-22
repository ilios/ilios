<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * InstructorGroup API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class InstructorGroupTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'instructorgroups';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadInstructorGroupData',
            'Tests\CoreBundle\Fixture\LoadSchoolData',
            'Tests\CoreBundle\Fixture\LoadTermData',
            'Tests\CoreBundle\Fixture\LoadLearnerGroupData',
            'Tests\CoreBundle\Fixture\LoadIlmSessionData',
            'Tests\CoreBundle\Fixture\LoadUserData',
            'Tests\CoreBundle\Fixture\LoadOfferingData',
            'Tests\CoreBundle\Fixture\LoadLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadSessionLearningMaterialData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text(60)],
            'school' => ['school', 2],
            'learnerGroups' => ['learnerGroups', [2, 3]],
            'ilmSessions' => ['ilmSessions', [1, 2]],
            'users' => ['users', [1]],
//            'offerings' => ['offerings', [2, 3, 4]],
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
            'ids' => [[1, 2], ['id' => [2, 3]]],
            'title' => [[1], ['title' => 'second instructor group']],
            'school' => [[0, 1, 2], ['school' => 1]],
            'schools' => [[0, 1, 2], ['schools' => [1]]],
//            'learnerGroups' => [[0], ['learnerGroups' => [1]]],
//            'ilmSessions' => [[0], ['ilmSessions' => [1]]],
//            'users' => [[0], ['users' => [1]]],
//            'offerings' => [[0], ['offerings' => [1]]],
            'courses' => [[0, 1], ['courses' => [1]]],
            'sessions' => [[0, 1], ['sessions' => [1, 2, 3]]],
            'sessionTypes' => [[0, 1, 2], ['sessionTypes' => [1, 2]]],
            'learningMaterials' => [[0], ['learningMaterials' => [1]]],
            'instructors' => [[0, 1, 2], ['instructors' => [2]]],
            'terms' => [[0, 1], ['terms' => [1, 2, 3]]],
        ];
    }

    public function testPostInstructorGroupIlmSession()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'instructorGroups', 'learnerGroups');
    }

    public function testPostInstructorGroupLearnerGroup()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'instructorGroups', 'ilmSessions');
    }
}
