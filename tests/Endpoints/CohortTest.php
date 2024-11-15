<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use PHPUnit\Framework\Attributes\Group;
use App\Tests\DataLoader\ProgramYearData;
use App\Tests\Fixture\LoadCohortData;
use App\Tests\Fixture\LoadCourseData;
use App\Tests\Fixture\LoadLearnerGroupData;
use App\Tests\Fixture\LoadProgramYearData;
use App\Tests\Fixture\LoadProgramYearObjectiveData;
use App\Tests\Fixture\LoadUserData;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cohort API endpoint Test.
 */
#[Group('api_2')]
class CohortTest extends AbstractReadEndpoint implements PutEndpointTestInterface
{
    use PutEndpointTestable;

    protected string $testName =  'cohorts';

    protected function getFixtures(): array
    {
        return [
            LoadCohortData::class,
            LoadProgramYearObjectiveData::class,
            LoadProgramYearData::class,
            LoadCourseData::class,
            LoadLearnerGroupData::class,
            LoadUserData::class,
        ];
    }

    public static function putsToTest(): array
    {
        return [
            'title' => ['title', 'dev null'],
            'courses' => ['courses', [1]],
            // 'learnerGroups' => ['learnerGroups', [1]], // skipped
            'users' => ['users', [1]],
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
            'title' => [[1], ['title' => 'Class of 2018']],
            'programYear' => [[1], ['programYear' => 2]],
            'courses' => [[2], ['courses' => [4]]],
            'learnerGroups' => [[1], ['learnerGroups' => [2]]],
            'users' => [[0], ['users' => [2]]],
            'schools' => [[3], ['schools' => [2]]],
            'startYears' => [[1, 3], ['startYears' => [2014, 2016]]],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[1, 2], ['ids' => [2, 3]]];

        return $filters;
    }

    public function testPostFails(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $this->createJsonRequest(
            'POST',
            '/api/' . $this->apiVersion . '/cohorts',
            json_encode(['cohort' => $data]),
            $jwt
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function testCreateWithPutFails(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $this->createJsonRequest(
            'PUT',
            $this->getUrl($this->kernelBrowser, 'app_api_cohorts_put', [
                'version' => $this->apiVersion,
                'id' => $data['id'],
            ]),
            json_encode(['cohort' => $data]),
            $jwt
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_GONE);
    }

    public function testDeleteFails(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $this->createJsonRequest(
            'DELETE',
            '/api/' . $this->apiVersion . '/cohorts/' . $data['id'],
            null,
            $jwt
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
    }

    /**
     * Unlock program years before attempting to PUT cohorts
     */
    protected function runPutForAllDataTest(string $jwt): void
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $putsToTest = $this->putsToTest();
        $firstPut = array_shift($putsToTest);
        $changeKey = $firstPut[0];
        $changeValue = $firstPut[1];

        foreach ($all as $cohort) {
            $programYearId = $cohort['programYear'];

            $programYear = $this->getProgramYear($programYearId);
            $programYear['locked'] = false;
            $programYear['archived'] = false;
            $this->putOne('programyears', 'programYear', $programYearId, $programYear, $jwt);
            $cohort[$changeKey] = $changeValue;
            $this->putTest($cohort, $cohort, $cohort['id'], $jwt);
        }
    }

    /**
     * Get programYear data from loader by id
     */
    protected function getProgramYear(int $id): array
    {
        $programYearDataLoader = self::getContainer()->get(ProgramYearData::class);
        $allProgramYears = $programYearDataLoader->getAll();
        $programYearsById = [];
        foreach ($allProgramYears as $arr) {
            $programYearsById[$arr['id']] = $arr;
        }

        return $programYearsById[$id];
    }
}
