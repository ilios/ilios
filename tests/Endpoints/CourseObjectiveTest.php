<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\DataLoader\CourseData;
use App\Tests\DataLoader\ObjectiveData;
use App\Tests\DataLoader\TermData;
use App\Tests\ReadWriteEndpointTest;

/**
 * CourseObjectiveTest API endpoint Test.
 * @group api_1
 */
class CourseObjectiveTest extends ReadWriteEndpointTest
{
    protected $testName =  'courseObjectives';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadObjectiveData',
            'App\Tests\Fixture\LoadTermData',
            'App\Tests\Fixture\LoadCourseData',
            'App\Tests\Fixture\LoadSessionData',
            'App\Tests\Fixture\LoadProgramYearData',
            'App\Tests\Fixture\LoadSessionObjectiveData',
            'App\Tests\Fixture\LoadCourseObjectiveData',
            'App\Tests\Fixture\LoadProgramYearObjectiveData',
        ];
    }

    /**
     * @inheritdoc
     */
    public function putsToTest()
    {
        return [
            'course' => ['course', 1],
            'objective' => ['objective', 2],
            'terms' => ['terms', [1, 4]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function readOnlyPropertiesToTest()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[1, 2], ['id' => [2, 3]]],
            'course' => [[1, 3], ['course' => 2]],
            'objective' => [[0, 1, 2], ['objective' => 2]],
            'terms' => [[0, 1], ['terms' => [1]]],
            'position' => [[0, 1, 2, 3, 4], ['position' => 0]],
        ];
    }

    protected function createMany(int $n): array
    {
        $objectiveDataLoader = $this->getContainer()->get(ObjectiveData::class);
        $objectives = $objectiveDataLoader->createMany($n);
        $savedObjectives = $this->postMany('objectives', 'objectives', $objectives);

        $courseDataLoader = $this->getContainer()->get(CourseData::class);
        $courses = $courseDataLoader->createMany($n);
        $savedCourses = $this->postMany('courses', 'courses', $courses);

        $dataLoader = $this->getDataLoader();

        $data = [];
        for ($i = 0; $i < $n; $i++) {
            $arr = $dataLoader->create();
            $arr['id'] += $i;
            $arr['course'] = $savedCourses[$i]['id'];
            $arr['objective'] = $savedObjectives[$i]['id'];
            $data[] = $arr;
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function testPostMany()
    {
        $data = $this->createMany(10);
        $this->postManyTest($data);
    }

    public function testPostManyJsonApi()
    {
        $data = $this->createMany(10);
        $jsonApiData = $this->getDataLoader()->createBulkJsonApi($data);
        $this->postManyJsonApiTest($jsonApiData, $data);
    }

    /**
     * @inheritdoc
     */
    public function testPutForAllData()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();

        $n = count($all);
        $termsDataLoader = $this->getContainer()->get(TermData::class);
        $terms = $termsDataLoader->createMany($n);
        $savedTerms = $this->postMany('terms', 'terms', $terms);

        for ($i = 0; $i < $n; $i++) {
            $data = $all[$i];
            $data['terms'][] = $savedTerms[$i]['id'];
            $this->putTest($data, $data, $data['id']);
        }
    }

    /**
     * @inheritdoc
     */
    public function testPatchForAllDataJsonApi()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();

        $n = count($all);
        $termsDataLoader = $this->getContainer()->get(TermData::class);
        $terms = $termsDataLoader->createMany($n);
        $savedTerms = $this->postMany('terms', 'terms', $terms);

        for ($i = 0; $i < $n; $i++) {
            $data = $all[$i];
            $data['terms'][] = $savedTerms[$i]['id'];
            $jsonApiData = $dataLoader->createJsonApi($data);
            $this->patchJsonApiTest($data, $jsonApiData);
        }
    }

    public function testRemoveLinksFromOrphanedObjectives()
    {
        $dataLoader = $this->getContainer()->get(ObjectiveData::class);
        $arr = $dataLoader->create();
        $arr['parents'] = ['1'];
        $arr['children'] = ['7', '8'];
        $arr['competency'] = 1;
        $arr['programYearObjectives'] = [];
        $arr['courseObjectives'] = [];
        $arr['sessionObjectives'] = [];
        unset($arr['id']);
        $objective = $this->postOne('objectives', 'objective', 'objectives', $arr);
        $dataLoader = $this->getContainer()->get(CourseData::class);
        $arr = $dataLoader->create();
        $course = $this->postOne('courses', 'course', 'courses', $arr);

        $dataLoader = $this->getDataLoader();
        $arr = $dataLoader->create();
        $arr['course'] = $course['id'];
        $arr['objective'] = $objective['id'];
        unset($arr['id']);
        $courseObjective = $this->postOne('courseobjectives', 'courseObjective', 'courseObjectives', $arr);

        $this->assertNotEmpty($objective['parents'], 'parents have been created');
        $this->assertNotEmpty($objective['children'], 'children have been created');
        $this->assertArrayHasKey('competency', $objective);

        $this->deleteTest($courseObjective['id']);

        $objective = $this->getOne('objectives', 'objectives', $objective['id']);

        $this->assertEmpty($objective['parents'], 'parents have been removed');
        $this->assertEmpty($objective['children'], 'children have been removed');
        $this->assertArrayNotHasKey('competency', $objective);
    }
}
