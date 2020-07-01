<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\DataLoader\CourseData;
use App\Tests\DataLoader\CourseObjectiveData;
use App\Tests\DataLoader\ProgramYearData;
use App\Tests\DataLoader\ProgramYearObjectiveData;
use App\Tests\DataLoader\SessionData;
use App\Tests\DataLoader\SessionObjectiveData;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\DataLoader\ObjectiveData;
use App\Tests\ReadWriteEndpointTest;

/**
 * Objective API endpoint Test.
 * @group api_5
 */
class ObjectiveTest extends ReadWriteEndpointTest
{
    protected $testName =  'objectives';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
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
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text],
            'position' => ['position', $this->getFaker()->randomDigit],
            'notActive' => ['active', false],
            'competency' => ['competency', 1],
            'parents' => ['parents', [2]],
            'children' => ['children', [4]],
            'meshDescriptors' => ['meshDescriptors', ['abc2']],
            'ancestor' => ['ancestor', 1, $skipped = true],
            'descendants' => ['descendants', [2], $skipped = true],
        ];
    }

    public function testPutXObjectives()
    {
        $dataLoader = $this->getDataLoader();
        $objective = $dataLoader->create();
        unset($objective['id']);
        $objective = $this->postOne('objectives', 'objective', 'objectives', $objective);

        $dataLoader = $this->getContainer()->get(CourseData::class);
        $course = $dataLoader->getOne();
        $dataLoader = $this->getContainer()->get(CourseObjectiveData::class);
        $courseObjective = $dataLoader->create();
        $courseObjective['course'] = $course['id'];
        $courseObjective['objective'] = $objective['id'];
        unset($courseObjective['id']);
        $courseObjective = $this->postOne('courseobjectives', 'courseObjective', 'courseObjectives', $courseObjective);

        $dataLoader = $this->getContainer()->get(ProgramYearData::class);
        $programYear = $dataLoader->getOne();
        $dataLoader = $this->getContainer()->get(ProgramYearObjectiveData::class);
        $programYearObjective = $dataLoader->create();
        $programYearObjective['programYear'] = $programYear['id'];
        $programYearObjective['objective'] = $objective['id'];
        unset($programYearObjective['id']);
        $programYearObjective = $this->postOne(
            'programyearobjectives',
            'programYearObjective',
            'programYearObjectives',
            $programYearObjective
        );

        $dataLoader = $this->getContainer()->get(SessionData::class);
        $session = $dataLoader->getOne();
        $dataLoader = $this->getContainer()->get(SessionObjectiveData::class);
        $sessionObjective = $dataLoader->create();
        $sessionObjective['session'] = $session['id'];
        $sessionObjective['objective'] = $objective['id'];
        unset($sessionObjective['id']);
        $sessionObjective = $this->postOne(
            'sessionobjectives',
            'sessionObjective',
            'sessionObjectives',
            $sessionObjective
        );

        $objective['courseObjectives'] = [ $courseObjective['id'] ];
        $objective['sessionObjectives'] = [ $sessionObjective['id'] ];
        $objective['programYearObjectives'] = [ $programYearObjective['id'] ];

        $this->putTest($objective, $objective, $objective['id']);
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
            'title' => [[1], ['title' => 'second objective']],
            'position' => [[0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10], ['position' => 0]],
            'active' => [[0, 1, 2, 3, 5, 7, 8, 9, 10], ['active' => 1]],
            'inactive' => [[4, 6], ['active' => 0]],
            'competency' => [[0], ['competency' => 3]],
            'courses' => [[1, 3], ['courses' => [2]]],
            'programYears' => [[0], ['programYears' => [1]]],
            'sessions' => [[2], ['sessions' => [1]]],
//            'parents' => [[2, 5], ['parents' => [2]]],
//            'children' => [[1], ['children' => [3]]],
//            'meshDescriptors' => [[6], ['meshDescriptors' => ['abc3']]],
            'ancestor' => [[6], ['ancestor' => 6]],
//            'descendants' => [[1], ['descendants' => [3]]],
            'fullCoursesThroughCourse' => [[1, 3], ['fullCourses' => [2]]],
            'fullCoursesThroughSession' => [[1, 2], ['fullCourses' => [1]]],
        ];
    }
}
