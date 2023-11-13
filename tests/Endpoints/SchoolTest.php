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

/**
 * School API endpoint Test.
 * @group api_5
 */
class SchoolTest extends AbstractReadWriteEndpoint
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
            'competencies' => [[0], ['competencies' => [1]], true],
            'courses' => [[0], ['courses' => [1]], true],
            'programs' => [[0], ['programs' => [1]], true],
            'vocabularies' => [[0], ['vocabularies' => [1]], true],
            'instructorGroups' => [[0], ['instructorGroups' => [1]], true],
            'curriculumInventoryInstitution' => [[0], ['curriculumInventoryInstitution' => 'test'], true],
            'sessionTypes' => [[0], ['sessionTypes' => [1]], true],
            'directors' => [[0], ['directors' => [1]], true],
            'administrators' => [[0], ['administrators' => [1]], true],
            'configurations' => [[0], ['configurations' => [1]], true],
        ];
    }

    public function graphQLFiltersToTest(): array
    {
        $filters = $this->filtersToTest();
        $filters['ids'] = [[1, 2], ['ids' => [2, 3]]];

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
