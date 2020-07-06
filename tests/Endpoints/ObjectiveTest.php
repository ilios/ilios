<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\AbstractEndpointTest;

/**
 * Objective API endpoint Test.
 * @group api_5
 */
class ObjectiveTest extends AbstractEndpointTest
{
    protected $apiVersion = 'v1';

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
     * @dataProvider filtersToTest
     * @param array $expectedIds
     * @param array $filterParts
     */
    public function testFilters(array $expectedIds = [], array $filterParts = [])
    {
        if (empty($filterParts)) {
            $this->markTestSkipped('Missing filters tests for this endpoint');
            return;
        }
        $filters = [];
        foreach ($filterParts as $key => $value) {
            $filters["filters[{$key}]"] = $value;
        }
        $objectives = $this->getFiltered('objectives', 'objectives', $filters);

        $actualIds = array_column($objectives, 'id');
        sort($actualIds);
        sort($expectedIds);
        $this->assertEquals($expectedIds, $actualIds);
    }

    public function filtersToTest(): array
    {
        return [
            'sessions' => [[3, 4, 5], ['sessions' => [1, 4]]],
            'session' => [[3], ['sessions' => [1]]],
            'courses' => [[6, 7, 9], ['courses' => [1, 2]]],
            'course' => [[6], ['courses' => [1]]],
            'programYears' => [[1, 2], ['programYears' => [1, 5]]],
            'programYear' => [[1], ['programYears' => [1]]],
            'sessionObjectives' => [[3, 4], ['sessionObjectives' => [1, 2]]],
            'sessionObjective' => [[3], ['sessionObjectives' => [1]]],
            'courseObjectives' => [[6, 7], ['courseObjectives' => [1, 2]]],
            'courseObjective' => [[6], ['courseObjectives' => [1]]],
            'programYearObjectives' => [[1, 2], ['programYearObjectives' => [1, 2]]],
            'programYearObjective' => [[1], ['programYearObjectives' => [1]]],
            'fullCourses' => [[3, 6, 7, 9], ['fullCourses' => [1, 2]]],
        ];
    }
}
