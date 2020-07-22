<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\V1ReadEndpointTest;

/**
 * School API v1 endpoint Test.
 * @group api_5
 */
class SchoolV1Test extends V1ReadEndpointTest
{
    protected $testName =  'schools';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadSchoolData',
            'App\Tests\Fixture\LoadSchoolConfigData',
            'App\Tests\Fixture\LoadAlertData',
            'App\Tests\Fixture\LoadCompetencyData',
            'App\Tests\Fixture\LoadSessionTypeData',
            'App\Tests\Fixture\LoadDepartmentData',
            'App\Tests\Fixture\LoadCurriculumInventoryInstitutionData',
            'App\Tests\Fixture\LoadProgramYearStewardData',
            'App\Tests\Fixture\LoadCourseData',
            'App\Tests\Fixture\LoadReportData',
            'App\Tests\Fixture\LoadInstructorGroupData',
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
            'title' => [[2], ['title' => 'third school']],
            'iliosAdministratorEmail' => [[1], ['iliosAdministratorEmail' => 'info@example.com']],
            'changeAlertRecipients' => [[2], ['changeAlertRecipients' => 'info@example.com']],
            'competencies' => [[0], ['competencies' => [1]], $skipped = true],
            'courses' => [[0], ['courses' => [1]], $skipped = true],
            'programs' => [[0], ['programs' => [1]], $skipped = true],
            'departments' => [[0], ['departments' => [1]], $skipped = true],
            'vocabularies' => [[0], ['vocabularies' => [1]], $skipped = true],
            'instructorGroups' => [[0], ['instructorGroups' => [1]], $skipped = true],
            'curriculumInventoryInstitution' => [[0], ['curriculumInventoryInstitution' => 'test'], $skipped = true],
            'sessionTypes' => [[0], ['sessionTypes' => [1]], $skipped = true],
            'stewards' => [[0], ['stewards' => [1]], $skipped = true],
            'directors' => [[0], ['directors' => [1]], $skipped = true],
            'administrators' => [[0], ['administrators' => [1]], $skipped = true],
            'configurations' => [[0], ['configurations' => [1]], $skipped = true],
        ];
    }
}
