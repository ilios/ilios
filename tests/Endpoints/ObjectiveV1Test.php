<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\DataLoader\CourseData;
use App\Tests\DataLoader\CourseObjectiveData;
use App\Tests\DataLoader\ProgramYearData;
use App\Tests\DataLoader\SessionData;
use App\Tests\DataLoader\SessionObjectiveData;
use App\Tests\DataLoader\ProgramYearObjectiveData;
use App\Tests\V1ReadEndpointTest;

/**
 * Objective V1 API endpoint Test.
 * @group api_5
 */
class ObjectiveV1Test extends V1ReadEndpointTest
{
    protected $testName =  'objectives';

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

        $v3ObjectiveInSession = $this->postOne(
            'objectives',
            'objective',
            'objectives',
            $objectiveInSessionData,
            'v3'
        );
        $v3ObjectiveInCourse = $this->postOne(
            'objectives',
            'objective',
            'objectives',
            $objectiveInCourseData,
            'v3'
        );
        $v3ObjectiveInProgramYear = $this->postOne(
            'objectives',
            'objective',
            'objectives',
            $objectiveInProgramYearData,
            'v3'
        );

        $v3Courses = $this->postMany(
            'courses',
            'courses',
            [ $courseData1, $courseData2 ],
            'v3'
        );
        $v3Session = $this->postOne(
            'sessions',
            'session',
            'sessions',
            $sessionData,
            'v3'
        );
        $v3ProgramYear = $this->postOne(
            'programyears',
            'programYear',
            'programYears',
            $programYearData,
            'v3'
        );

        $courseObjectiveData1 = $courseObjectiveDataLoader->create();
        unset($courseObjectiveData1['id']);
        $courseObjectiveData1['objective'] = $v3ObjectiveInCourse['id'];
        $courseObjectiveData1['course'] = $v3Courses[0]['id'];
        $courseObjectiveData1['position'] = 3;
        $courseObjectiveData2 = $courseObjectiveDataLoader->create();
        unset($courseObjectiveData2['id']);
        $courseObjectiveData2['objective'] = $v3ObjectiveInCourse['id'];
        $courseObjectiveData2['course'] = $v3Courses[1]['id'];
        $courseObjectiveData2['position'] = 5;
        $sessionObjectiveData = $sessionObjectiveDataLoader->create();
        unset($sessionObjectiveData['id']);
        $sessionObjectiveData['objective'] = $v3ObjectiveInSession['id'];
        $sessionObjectiveData['session'] = $v3Session['id'];
        $sessionObjectiveData['position'] = 3;
        $programYearObjectiveData = $programYearObjectiveDataLoader->create();
        unset($programYearObjectiveData['id']);
        $programYearObjectiveData['objective'] = $v3ObjectiveInProgramYear['id'];
        $programYearObjectiveData['programYear'] = $v3ProgramYear['id'];

        $this->postMany(
            'courseobjectives',
            'courseObjectives',
            [ $courseObjectiveData1, $courseObjectiveData2 ],
            'v3'
        );
        $this->postOne(
            'sessionobjectives',
            'sessionObjective',
            'sessionObjectives',
            $sessionObjectiveData,
            'v3'
        );
        $this->postOne(
            'programyearobjectives',
            'programYearObjective',
            'programYearObjectives',
            $programYearObjectiveData,
            'v3'
        );

        $v1ObjectiveInCourse = $this->getOne('objectives', 'objectives', $v3ObjectiveInCourse['id']);
        $this->assertEquals($v1ObjectiveInCourse['id'], $v3ObjectiveInCourse['id']);
        $this->assertEquals($v1ObjectiveInCourse['title'], $v3ObjectiveInCourse['title']);
        $this->assertEquals($v1ObjectiveInCourse['competency'], $v3ObjectiveInCourse['competency']);
        $this->assertEquals($v1ObjectiveInCourse['parents'], $v3ObjectiveInCourse['parents']);
        $this->assertEquals($v1ObjectiveInCourse['children'], $v3ObjectiveInCourse['children']);
        $this->assertEquals($v1ObjectiveInCourse['descendants'], $v3ObjectiveInCourse['descendants']);
        $this->assertEquals($v1ObjectiveInCourse['meshDescriptors'], $v3ObjectiveInCourse['meshDescriptors']);
        $this->assertEquals($v1ObjectiveInCourse['active'], $v3ObjectiveInCourse['active']);
        $this->assertEquals($v1ObjectiveInCourse['position'], $courseObjectiveData1['position']);
        $this->assertCount(2, $v1ObjectiveInCourse['courses']);
        $this->assertEquals($v1ObjectiveInCourse['courses'][0], $v3Courses[0]['id']);
        $this->assertEquals($v1ObjectiveInCourse['courses'][1], $v3Courses[1]['id']);
        $this->assertEmpty($v1ObjectiveInCourse['sessions']);
        $this->assertEmpty($v1ObjectiveInCourse['programYears']);

        $v3ObjectiveInSession = $this->getOne('objectives', 'objectives', $v3ObjectiveInSession['id']);
        $this->assertCount(1, $v3ObjectiveInSession['sessions']);
        $this->assertEquals($v3ObjectiveInSession['sessions'][0], $v3Session['id']);
        $this->assertEquals($v3ObjectiveInSession['position'], $sessionObjectiveData['position']);

        $v1ObjectiveInProgramYear = $this->getOne('objectives', 'objectives', $v3ObjectiveInProgramYear['id']);
        $this->assertCount(1, $v1ObjectiveInProgramYear['programYears']);
        $this->assertEquals($v1ObjectiveInProgramYear['programYears'][0], $v3ProgramYear['id']);
        $this->assertEquals($v1ObjectiveInProgramYear['position'], 0);
    }
}
