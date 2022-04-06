<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadAlertData;
use App\Tests\Fixture\LoadCompetencyData;
use App\Tests\Fixture\LoadCourseData;
use App\Tests\Fixture\LoadCurriculumInventoryInstitutionData;
use App\Tests\Fixture\LoadInstructorGroupData;
use App\Tests\Fixture\LoadReportData;
use App\Tests\Fixture\LoadSchoolConfigData;
use App\Tests\Fixture\LoadSchoolData;
use App\Tests\Fixture\LoadSessionTypeData;
use App\Tests\ReadWriteEndpointTest;

/**
 * School API endpoint Test.
 * @group api_5
 */
class SchoolTest extends ReadWriteEndpointTest
{
    protected string $testName =  'schools';

    protected function getFixtures(): array
    {
        return [
            LoadSchoolData::class,
            LoadSchoolConfigData::class,
            LoadAlertData::class,
            LoadCompetencyData::class,
            LoadSessionTypeData::class,
            LoadCurriculumInventoryInstitutionData::class,
            LoadCourseData::class,
            LoadReportData::class,
            LoadInstructorGroupData::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest(): array
    {
        return [
            'iliosAdministratorEmail' => ['iliosAdministratorEmail', 'lorem.ipsum@dev.null'],
            'title' => ['title', 'university 123'],
            'templatePrefix' => ['templatePrefix', 'u213'],
            'changeAlertRecipients' => ['changeAlertRecipients', 'please.dont@email.me'],
            'competencies' => ['competencies', [1], $skipped = true],
            'courses' => ['courses', [1], $skipped = true],
            'programs' => ['programs', [1], $skipped = true],
            'vocabularies' => ['vocabularies', [2], $skipped = true],
            'instructorGroups' => ['instructorGroups', [1], $skipped = true],
            'curriculumInventoryInstitution' => ['curriculumInventoryInstitution', 3, $skipped = true],
            'sessionTypes' => ['sessionTypes', [1], $skipped = true],
            'directors' => ['directors', [2]],
            'administrators' => ['administrators', [2]],
            'configurations' => ['configurations', [1], $skipped = true],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest(): array
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest(): array
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
            'vocabularies' => [[0], ['vocabularies' => [1]], $skipped = true],
            'instructorGroups' => [[0], ['instructorGroups' => [1]], $skipped = true],
            'curriculumInventoryInstitution' => [[0], ['curriculumInventoryInstitution' => 'test'], $skipped = true],
            'sessionTypes' => [[0], ['sessionTypes' => [1]], $skipped = true],
            'directors' => [[0], ['directors' => [1]], $skipped = true],
            'administrators' => [[0], ['administrators' => [1]], $skipped = true],
            'configurations' => [[0], ['configurations' => [1]], $skipped = true],
        ];
    }

    /**
     * We can't test deleting schools as sqlite doesn't enforce FK cascades
     * This leaves us with bad data in the database which fails the tests
     * when the SessionUser attempts to build its permission tree
     */
    public function testDelete()
    {
        $this->assertTrue(true);
    }
}
