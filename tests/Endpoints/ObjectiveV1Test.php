<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\DataLoader\CourseData;
use App\Tests\DataLoader\CourseObjectiveData;
use App\Tests\DataLoader\ProgramYearData;
use App\Tests\DataLoader\SessionData;
use App\Tests\DataLoader\SessionObjectiveData;
use App\Tests\DataLoader\ProgramYearObjectiveData;
use App\Tests\ReadEndpointTest;

/**
 * Objective V1 API endpoint Test.
 * @group api_5
 */
class ObjectiveV1Test extends ReadEndpointTest
{
    protected $testName =  'objectives';

    protected $apiVersion = 'v1';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadObjectiveData',
            'App\Tests\Fixture\LoadCompetencyData',
            'App\Tests\Fixture\LoadCourseData',
            'App\Tests\Fixture\LoadProgramYearData',
            'App\Tests\Fixture\LoadSessionData',
            'App\Tests\Fixture\LoadMeshDescriptorData',
            'App\Tests\Fixture\LoadSessionObjectiveData',
            'App\Tests\Fixture\LoadCourseObjectiveData',
            'App\Tests\Fixture\LoadProgramYearObjectiveData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function testGetOne()
    {
        $objectiveDataLoader = $this->getDataLoader();
        $sessionDataLoader = $this->getContainer()->get(SessionData::class);
        $courseDataLoader = $this->getContainer()->get(CourseData::class);
        $programYearDataLoader = $this->getContainer()->get(ProgramYearData::class);
        $sessionObjectiveDataLoader = $this->getContainer()->get(SessionObjectiveData::class);
        $courseObjectiveDataLoader = $this->getContainer()->get(CourseObjectiveData::class);
        $programYearObjectiveDataLoader = $this->getContainer()->get(ProgramYearObjectiveData::class);

        $objectiveInSessionData = $objectiveDataLoader->create();
        unset($objectiveInSessionData['id']);
        $objectiveInCourseData = $objectiveDataLoader->create();
        unset($objectiveInCourseData['id']);
        $objectiveInProgramYearData = $objectiveDataLoader->create();
        unset($objectiveInProgramYearData['id']);
        $courseData1 = $courseDataLoader->create();
        unset($courseData1['id']);
        $courseData2 = $courseDataLoader->create();
        unset($courseData2['id']);
        $sessionData = $sessionDataLoader->create();
        unset($sessionData['id']);
        $programYearData = $programYearDataLoader->create();
        unset($programYearData['id']);

        $v2ObjectiveInSession = $this->postOne(
            'objectives',
            'objective',
            'objectives',
            $objectiveInSessionData,
            'v2'
        );
        $v2ObjectiveInCourse = $this->postOne(
            'objectives',
            'objective',
            'objectives',
            $objectiveInCourseData,
            'v2'
        );
        $v2ObjectiveInProgramYear = $this->postOne(
            'objectives',
            'objective',
            'objectives',
            $objectiveInProgramYearData,
            'v2'
        );

        $v2Courses = $this->postMany(
            'courses',
            'courses',
            [ $courseData1, $courseData2 ],
            'v2'
        );
        $v2Session = $this->postOne(
            'sessions',
            'session',
            'sessions',
            $sessionData,
            'v2'
        );
        $v2ProgramYear = $this->postOne(
            'programyears',
            'programYear',
            'programYears',
            $programYearData,
            'v2'
        );

        $courseObjectiveData1 = $courseObjectiveDataLoader->create();
        unset($courseObjectiveData1['id']);
        $courseObjectiveData1['objective'] = $v2ObjectiveInCourse['id'];
        $courseObjectiveData1['course'] = $v2Courses[0]['id'];
        $courseObjectiveData1['position'] = 3;
        $courseObjectiveData2 = $courseObjectiveDataLoader->create();
        unset($courseObjectiveData2['id']);
        $courseObjectiveData2['objective'] = $v2ObjectiveInCourse['id'];
        $courseObjectiveData2['course'] = $v2Courses[1]['id'];
        $courseObjectiveData2['position'] = 5;
        $sessionObjectiveData = $sessionObjectiveDataLoader->create();
        unset($sessionObjectiveData['id']);
        $sessionObjectiveData['objective'] = $v2ObjectiveInSession['id'];
        $sessionObjectiveData['session'] = $v2Session['id'];
        $sessionObjectiveData['position'] = 3;
        $programYearObjectiveData = $programYearObjectiveDataLoader->create();
        unset($programYearObjectiveData['id']);
        $programYearObjectiveData['objective'] = $v2ObjectiveInProgramYear['id'];
        $programYearObjectiveData['programYear'] = $v2ProgramYear['id'];

        $this->postMany(
            'courseobjectives',
            'courseObjectives',
            [ $courseObjectiveData1, $courseObjectiveData2 ],
            'v2'
        );
        $this->postOne(
            'sessionobjectives',
            'sessionObjective',
            'sessionObjectives',
            $sessionObjectiveData,
            'v2'
        );
        $this->postOne(
            'programyearobjectives',
            'programYearObjective',
            'programYearObjectives',
            $programYearObjectiveData,
            'v2'
        );

        $v1ObjectiveInCourse = $this->getOne('objectives', 'objectives', $v2ObjectiveInCourse['id']);
        $this->assertEquals($v1ObjectiveInCourse['id'], $v2ObjectiveInCourse['id']);
        $this->assertEquals($v1ObjectiveInCourse['title'], $v2ObjectiveInCourse['title']);
        $this->assertEquals($v1ObjectiveInCourse['competency'], $v2ObjectiveInCourse['competency']);
        $this->assertEquals($v1ObjectiveInCourse['parents'], $v2ObjectiveInCourse['parents']);
        $this->assertEquals($v1ObjectiveInCourse['children'], $v2ObjectiveInCourse['children']);
        $this->assertEquals($v1ObjectiveInCourse['descendants'], $v2ObjectiveInCourse['descendants']);
        $this->assertEquals($v1ObjectiveInCourse['meshDescriptors'], $v2ObjectiveInCourse['meshDescriptors']);
        $this->assertEquals($v1ObjectiveInCourse['active'], $v2ObjectiveInCourse['active']);
        $this->assertEquals($v1ObjectiveInCourse['position'], $courseObjectiveData1['position']);
        $this->assertCount(2, $v1ObjectiveInCourse['courses']);
        $this->assertEquals($v1ObjectiveInCourse['courses'][0], $v2Courses[0]['id']);
        $this->assertEquals($v1ObjectiveInCourse['courses'][1], $v2Courses[1]['id']);
        $this->assertEmpty($v1ObjectiveInCourse['sessions']);
        $this->assertEmpty($v1ObjectiveInCourse['programYears']);

        $v2ObjectiveInSession = $this->getOne('objectives', 'objectives', $v2ObjectiveInSession['id']);
        $this->assertCount(1, $v2ObjectiveInSession['sessions']);
        $this->assertEquals($v2ObjectiveInSession['sessions'][0], $v2Session['id']);
        $this->assertEquals($v2ObjectiveInSession['position'], $sessionObjectiveData['position']);

        $v1ObjectiveInProgramYear = $this->getOne('objectives', 'objectives', $v2ObjectiveInProgramYear['id']);
        $this->assertCount(1, $v1ObjectiveInProgramYear['programYears']);
        $this->assertEquals($v1ObjectiveInProgramYear['programYears'][0], $v2ProgramYear['id']);
        $this->assertEquals($v1ObjectiveInProgramYear['position'], 0);
    }

    /**
     * @inheritDoc
     */
    public function testGetAll()
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $v1url = $this->getUrl(
            $this->kernelBrowser,
            'ilios_api_getall',
            ['version' => $this->apiVersion, 'object' => $endpoint]
        );
        $v2url = $this->getUrl(
            $this->kernelBrowser,
            'ilios_api_getall',
            ['version' => 'v2', 'object' => $endpoint]
        );
        $this->createJsonRequest(
            'GET',
            $v1url,
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $v1Response = $this->kernelBrowser->getResponse();

        $this->createJsonRequest(
            'GET',
            $v2url,
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $v2Response = $this->kernelBrowser->getResponse();

        $v1Data = json_decode($v1Response->getContent(), true)[$responseKey];
        $v2Data = json_decode($v2Response->getContent(), true)[$responseKey];

        $this->assertNotEmpty($v1Data);
        $this->assertEquals(count($v2Data), count($v1Data));
        $v1Ids = array_column($v1Data, 'id');
        $v2Ids = array_column($v1Data, 'id');
        $this->assertEquals($v2Ids, $v1Ids);
    }
}
