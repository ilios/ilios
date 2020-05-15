<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\V1ReadEndpointTest;

/**
 * Course API v1 endpoint Test.
 * @group api_2
 */
class CourseV1Test extends V1ReadEndpointTest
{
    protected $testName =  'courses';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadCourseData',
            'App\Tests\Fixture\LoadCourseClerkshipTypeData',
            'App\Tests\Fixture\LoadSchoolData',
            'App\Tests\Fixture\LoadUserData',
            'App\Tests\Fixture\LoadCohortData',
            'App\Tests\Fixture\LoadTermData',
            'App\Tests\Fixture\LoadObjectiveData',
            'App\Tests\Fixture\LoadCourseLearningMaterialData',
            'App\Tests\Fixture\LoadSessionData',
            'App\Tests\Fixture\LoadSessionDescriptionData',
            'App\Tests\Fixture\LoadOfferingData',
            'App\Tests\Fixture\LoadProgramYearData',
            'App\Tests\Fixture\LoadSessionLearningMaterialData',
            'App\Tests\Fixture\LoadIlmSessionData',
            'App\Tests\Fixture\LoadProgramYearObjectiveData',
            'App\Tests\Fixture\LoadCourseObjectiveData',
            'App\Tests\Fixture\LoadSessionObjectiveData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function testGetOne()
    {
        $courseData = $this->getDataLoader()->getOne();
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $v1Course = $this->getOne($endpoint, $responseKey, $courseData['id']);
        $v2Course = $this->getOne($endpoint, $responseKey, $courseData['id'], 'v2');
        $courseObjective = $this->getOne(
            'courseobjectives',
            'courseObjectives',
            $v2Course['courseObjectives'][0],
            'v2'
        );
        $this->assertEquals($v2Course['id'], $v1Course['id']);
        $this->assertEquals($v2Course['title'], $v1Course['title']);
        $this->assertEquals($v2Course['level'], $v1Course['level']);
        $this->assertEquals($v2Course['year'], $v1Course['year']);
        $this->assertEquals($v2Course['startDate'], $v1Course['startDate']);
        $this->assertEquals($v2Course['endDate'], $v1Course['endDate']);
        $this->assertEquals($v2Course['externalId'], $v1Course['externalId']);
        $this->assertEquals($v2Course['archived'], $v1Course['archived']);
        $this->assertEquals($v2Course['publishedAsTbd'], $v1Course['publishedAsTbd']);
        $this->assertEquals($v2Course['clerkshipType'], $v1Course['clerkshipType']);
        $this->assertEquals($v2Course['school'], $v1Course['school']);
        $this->assertEquals($v2Course['directors'], $v1Course['directors']);
        $this->assertEquals($v2Course['administrators'], $v1Course['administrators']);
        $this->assertEquals($v2Course['cohorts'], $v1Course['cohorts']);
        $this->assertEquals($v2Course['terms'], $v1Course['terms']);
        $this->assertEquals($v2Course['meshDescriptors'], $v1Course['meshDescriptors']);
        $this->assertEquals($v2Course['learningMaterials'], $v1Course['learningMaterials']);
        $this->assertEquals($v2Course['sessions'], $v1Course['sessions']);
        $this->assertEquals($v2Course['descendants'], $v1Course['descendants']);
        $this->assertEquals(count($v2Course['courseObjectives']), count($v1Course['objectives']));
        $this->assertEquals($courseObjective['objective'], $v1Course['objectives'][0]);
    }

    /**
     * @see CourseTest::testGetMyCourses()
     */
    public function testGetMyCourses()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $userId = 2;
        $filteredData = $this->getFiltered($endpoint, $responseKey, ['my' => true], $userId);
        $this->assertCount(3, $filteredData);
        $this->assertEquals($all[0]['id'], $filteredData[0]['id']);
        $this->assertEquals($all[1]['id'], $filteredData[1]['id']);
        $this->assertEquals($all[3]['id'], $filteredData[2]['id']);
    }

    /**
     * @see CourseTest::testGetMyCoursesSorted()
     */
    public function testGetMyCoursesSorted()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $userId = 2;
        $filteredData = $this->getFiltered(
            $endpoint,
            $responseKey,
            ['my' => true, 'order_by[year]' => 'ASC', 'order_by[id]' => 'DESC'],
            $userId
        );
        $this->assertCount(3, $filteredData);
        $this->assertEquals($all[1]['id'], $filteredData[0]['id']);
        $this->assertEquals($all[3]['id'], $filteredData[1]['id']);
        $this->assertEquals($all[0]['id'], $filteredData[2]['id']);
    }

    /**
     * @see CourseTest::testGetMyCoursesFailureOnBogusOrderBy()
     */
    public function testGetMyCoursesFailureOnBogusOrderBy()
    {
        $this->badFilterTest(
            ['my' => true, 'order_by[glefarknik]' => 'ASC']
        );
    }

    /**
     * @see CourseTest::testGetMyCoursesFailureOnBogusFilterBy()
     */
    public function testGetMyCoursesFailureOnBogusFilterBy()
    {
        $this->badFilterTest(
            ['my' => true, 'filters[farnk]' => 1]
        );
    }

    /**
     * @see CourseTest::testGetMyCoursesWithLimit()
     */
    public function testGetMyCoursesWithLimit()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $userId = 2;
        $filteredData = $this->getFiltered(
            $endpoint,
            $responseKey,
            ['my' => true, 'limit' => 2],
            $userId
        );
        $this->assertCount(2, $filteredData);
        $this->assertEquals($all[0]['id'], $filteredData[0]['id']);
        $this->assertEquals($all[1]['id'], $filteredData[1]['id']);
    }

    /**
     * @see CourseTest::testGetMyCoursesWithLimitAndOffset()
     */
    public function testGetMyCoursesWithLimitAndOffset()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $userId = 2;
        $filteredData = $this->getFiltered(
            $endpoint,
            $responseKey,
            ['my' => true, 'limit' => 1, 'offset' => 1],
            $userId
        );
        $this->assertCount(1, $filteredData);
        $this->assertEquals($all[1]['id'], $filteredData[0]['id']);
    }

    /**
     * @see CourseTest::testGetMyCoursesFilteredByYear()
     */
    public function testGetMyCoursesFilteredByYear()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $userId = 2;

        $filteredData = $this->getFiltered(
            $endpoint,
            $responseKey,
            ['my' => true, 'filters[year]' => '2012'],
            $userId
        );
        $this->assertCount(1, $filteredData);
        $this->assertEquals($all[1]['id'], $filteredData[0]['id']);

        $filteredData = $this->getFiltered(
            $endpoint,
            $responseKey,
            ['my' => true, 'filters[year]' => '2013'],
            $userId
        );
        $this->assertCount(1, $filteredData);
        $this->assertEquals($all[3]['id'], $filteredData[0]['id']);

        $filteredData = $this->getFiltered(
            $endpoint,
            $responseKey,
            ['my' => true, 'filters[year]' => ['2012', '2013']],
            $userId
        );
        $this->assertCount(2, $filteredData);
        $this->assertEquals($all[1]['id'], $filteredData[0]['id']);
        $this->assertEquals($all[3]['id'], $filteredData[1]['id']);
    }

    /**
     * @see CourseTest::testGetMyCoursesIncludesAdministeredCourses()
     */
    public function testGetMyCoursesIncludesAdministeredCourses()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $userId = 4;

        $filteredData = $this->getFiltered(
            $endpoint,
            $responseKey,
            ['my' => true],
            $userId
        );
        $this->assertCount(3, $filteredData);
        $this->assertEquals($all[0]['id'], $filteredData[0]['id']);
        $this->assertEquals($all[2]['id'], $filteredData[1]['id']);
        $this->assertEquals($all[4]['id'], $filteredData[2]['id']);
    }
}
