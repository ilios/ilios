<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use PHPUnit\Framework\Attributes\Group;
use App\Tests\Fixture\LoadAlertData;
use App\Tests\Fixture\LoadCompetencyData;
use App\Tests\Fixture\LoadCourseData;
use App\Tests\Fixture\LoadCurriculumInventoryInstitutionData;
use App\Tests\Fixture\LoadInstructorGroupData;
use App\Tests\Fixture\LoadReportData;
use App\Tests\Fixture\LoadSchoolConfigData;
use App\Tests\Fixture\LoadSchoolData;
use App\Tests\Fixture\LoadSessionTypeData;

/**
 * School API endpoint Test.
 */
#[Group('api_5')]
final class SchoolTest extends AbstractReadWriteEndpoint
{
    protected string $testName =  'schools';

    protected bool $enablePostTestsWithServiceToken = false;

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

    public static function putsToTest(): array
    {
        return [
            'iliosAdministratorEmail' => ['iliosAdministratorEmail', 'lorem.ipsum@dev.null'],
            'title' => ['title', 'university 123'],
            'templatePrefix' => ['templatePrefix', 'u213'],
            'changeAlertRecipients' => ['changeAlertRecipients', 'please.dont@email.me'],
            // 'competencies' => ['competencies', [1]], // skipped
            // 'courses' => ['courses', [1]], // skipped
            // 'programs' => ['programs', [1]], // skipped
            // 'vocabularies' => ['vocabularies', [2]], // skipped
            // 'instructorGroups' => ['instructorGroups', [1]], // skipped
            // 'curriculumInventoryInstitution' => ['curriculumInventoryInstitution', 3], // skipped
            // 'sessionTypes' => ['sessionTypes', [1]], // skipped
            'directors' => ['directors', [2]],
            'administrators' => ['administrators', [2]],
            // 'configurations' => ['configurations', [1]], // skipped
        ];
    }

    public static function readOnlyPropertiesToTest(): array
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    public static function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[1, 2], ['id' => [2, 3]]],
            'missingId' => [[], ['id' => 99]],
            'missingIds' => [[], ['id' => [99]]],
            'title' => [[2], ['title' => 'third school']],
            'iliosAdministratorEmail' => [[1], ['iliosAdministratorEmail' => 'info@example.com']],
            'changeAlertRecipients' => [[2], ['changeAlertRecipients' => 'info@example.com']],
            // 'competencies' => [[0], ['competencies' => [1]]], // skipped
            // 'courses' => [[0], ['courses' => [1]]], // skipped
            // 'programs' => [[0], ['programs' => [1]]], // skipped
            // 'vocabularies' => [[0], ['vocabularies' => [1]]], // skipped
            // 'instructorGroups' => [[0], ['instructorGroups' => [1]]], // skipped
            // 'curriculumInventoryInstitution' => [[0], ['curriculumInventoryInstitution' => 'test']], // skipped
            // 'sessionTypes' => [[0], ['sessionTypes' => [1]]], // skipped
            // 'directors' => [[0], ['directors' => [1]]], // skipped
            // 'administrators' => [[0], ['administrators' => [1]]], // skipped
            // 'configurations' => [[0], ['configurations' => [1]]], // skipped
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[1, 2], ['ids' => [2, 3]]];
        $filters['missingIds'] = [[], ['ids' => [99]]];

        return $filters;
    }

    /**
     * We can't test deleting schools as sqlite doesn't enforce FK cascades
     * This leaves us with bad data in the database which fails the tests
     * when the SessionUser attempts to build its permission tree
     */
    protected function runDeleteTest(string $jwt): void
    {
        $this->markTestSkipped('intentionally skipped.');
    }

    public function testAccessDeniedWithServiceToken(): void
    {
        $jwt = $this->createJwtFromServiceTokenWithWriteAccessInAllSchools(
            $this->kernelBrowser,
            $this->fixtures
        );
        $data = $this->getDataLoader()->getOne();
        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'DELETE',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_schools_delete',
                ['version' => $this->apiVersion, 'id' => $data['id']],
            ),
        );
        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_schools_post',
                ['version' => $this->apiVersion],
            ),
            json_encode([])
        );
        $this->canNotJsonApi(
            $this->kernelBrowser,
            $jwt,
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_schools_post',
                ['version' => $this->apiVersion],
            ),
            json_encode([])
        );
    }
}
