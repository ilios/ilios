<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadCourseData;
use App\Tests\Fixture\LoadCurriculumInventoryReportData;
use App\Tests\Fixture\LoadProgramData;
use App\Tests\Fixture\LoadProgramYearData;
use App\Tests\Fixture\LoadSchoolData;
use App\Tests\Fixture\LoadSessionData;
use App\Tests\Fixture\LoadTermData;

/**
 * Program API endpoint Test.
 */
#[\PHPUnit\Framework\Attributes\Group('api_1')]
class ProgramTest extends AbstractReadWriteEndpoint
{
    protected string $testName =  'programs';

    protected function getFixtures(): array
    {
        return [
            LoadProgramData::class,
            LoadTermData::class,
            LoadSchoolData::class,
            LoadProgramYearData::class,
            LoadCourseData::class,
            LoadSessionData::class,
            LoadCurriculumInventoryReportData::class,
        ];
    }

    public static function putsToTest(): array
    {
        return [
            'title' => ['title', 'history of consciousness'],
            'shortTitle' => ['shortTitle', 'histcon'],
            'duration' => ['duration', 12],
            'school' => ['school', 3],
            // 'programYears' => ['programYears', [1]], // skipped
            // 'curriculumInventoryReports' => ['curriculumInventoryReports', [1]], // skipped
            'directors' => ['directors', [1, 3]],
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
            'ids' => [[0, 2], ['id' => [1, 3]]],
            'title' => [[1], ['title' => 'second program']],
            'shortTitle' => [[0], ['shortTitle' => 'fp']],
            'duration' => [[1, 2], ['duration' => 4]],
            'school' => [[2], ['school' => 2]],
            'schools' => [[0, 1], ['schools' => 1]],
            // 'programYears' => [[0], ['programYears' => [1]]], // skipped
            // 'curriculumInventoryReports' => [[0], ['curriculumInventoryReports' => [1]]], // skipped
            // 'directors' => [[0], ['directors' => [1]]], // skipped
            'durationAndSchool' => [[1], ['school' => 1, 'duration' => 4]],
            'courses' => [[1], ['courses' => [4]]],
            'sessions' => [[0], ['sessions' => [3]]],
            'terms' => [[0], ['terms' => [1]]],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[0, 2], ['ids' => [1, 3]]];

        return $filters;
    }

    public function testRejectUnprivilegedPostProgram(): void
    {
        $dataLoader = $this->getDataLoader();
        $program = $dataLoader->getOne();
        $jwt = $this->createJwtFromUserId($this->kernelBrowser, 3);

        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_programs_post',
                ['version' => $this->apiVersion]
            ),
            json_encode(['programs' => [$program]])
        );
    }

    public function testRejectUnprivilegedPutProgram(): void
    {
        $dataLoader = $this->getDataLoader();
        $program = $dataLoader->getOne();
        $jwt = $this->createJwtFromUserId($this->kernelBrowser, 3);

        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'PUT',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_programs_put',
                ['version' => $this->apiVersion, 'id' => $program['id']]
            ),
            json_encode(['program' => $program])
        );
    }

    public function testRejectUnprivilegedPutNewProgram(): void
    {
        $dataLoader = $this->getDataLoader();
        $program = $dataLoader->getOne();
        $jwt = $this->createJwtFromUserId($this->kernelBrowser, 3);

        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'PUT',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_programs_put',
                ['version' => $this->apiVersion, 'id' => $program['id'] * 10000]
            ),
            json_encode(['program' => $program])
        );
    }

    public function testRejectUnprivilegedDeleteProgram(): void
    {
        $dataLoader = $this->getDataLoader();
        $program = $dataLoader->getOne();
        $jwt = $this->createJwtFromUserId($this->kernelBrowser, 3);

        $this->canNot(
            $this->kernelBrowser,
            $jwt,
            'DELETE',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_programs_delete',
                ['version' => $this->apiVersion, 'id' => $program['id']]
            )
        );
    }

    public function testGraphQLIncludedData(): void
    {
        $loader = $this->getDataLoader();
        $data = $loader->getOne();

        $this->createGraphQLRequest(
            json_encode([
                'query' => "query { programs(id: {$data['id']}) { id, school { id } }}",
            ]),
            $this->createJwtForRootUser($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertGraphQLResponse($response);
        $content = json_decode($response->getContent());
        $this->assertIsObject($content->data);
        $this->assertIsArray($content->data->programs);
        $this->assertCount(1, $content->data->programs);
        $program = $content->data->programs[0];
        $this->assertTrue(property_exists($program, 'id'));
        $this->assertEquals($data['id'], $program->id);
        $this->assertTrue(property_exists($program, 'school'));
        $this->assertTrue(property_exists($program->school, 'id'));
        $this->assertEquals($data['school'], $program->school->id);
    }

    /**
     * Delete Program 2 explicitly as Program 1 is linked
     * to School 1.  Since sqlite doesn't cascade this doesn't work
     */
    protected function runDeleteTest(string $jwt): void
    {
        $this->deleteTest(2, $jwt);
    }
}
