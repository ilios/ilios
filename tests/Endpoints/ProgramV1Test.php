<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\V1ReadEndpointTest;

/**
 * Program API endpoint Test.
 * @group api_1
 */
class ProgramV1Test extends V1ReadEndpointTest
{
    protected $testName =  'programs';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadProgramData',
            'App\Tests\Fixture\LoadTermData',
            'App\Tests\Fixture\LoadSchoolData',
            'App\Tests\Fixture\LoadProgramYearData',
            'App\Tests\Fixture\LoadCourseData',
            'App\Tests\Fixture\LoadSessionData',
            'App\Tests\Fixture\LoadCurriculumInventoryReportData'
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
            'ids' => [[0, 2], ['id' => [1, 3]]],
            'title' => [[1], ['title' => 'second program']],
            'shortTitle' => [[0], ['shortTitle' => 'fp']],
            'duration' => [[1, 2], ['duration' => 4]],
            'publishedAsTbd' => [[1], ['publishedAsTbd' => true]],
            'notPublishedAsTbd' => [[0, 2], ['publishedAsTbd' => false]],
            'published' => [[0], ['published' => true]],
            'notPublished' => [[1, 2], ['published' => false]],
            'school' => [[2], ['school' => 2]],
            'schools' => [[0, 1], ['schools' => 1]],
            'programYears' => [[0], ['programYears' => [1]], $skipped = true],
            'curriculumInventoryReports' => [[0], ['curriculumInventoryReports' => [1]], $skipped = true],
            'directors' => [[0], ['directors' => [1]], $skipped = true],
            'durationAndScheduled' => [[1], ['publishedAsTbd' => true, 'duration' => 4]],
            'durationAndSchool' => [[1], ['school' => 1, 'duration' => 4]],
            'courses' => [[1], ['courses' => [4]]],
            'sessions' => [[0], ['sessions' => [3]]],
            'terms' => [[0], ['terms' => [1]]],
        ];
    }
}
