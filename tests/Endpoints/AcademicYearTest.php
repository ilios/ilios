<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Entity\Course;
use App\Tests\Fixture\LoadCourseData;
use Symfony\Component\HttpFoundation\Response;

/**
 * AamcMethod API endpoint Test.
 * @group api_5
 */
class AcademicYearTest extends AbstractReadEndpoint
{
    protected string $testName = 'academicYears';

    protected bool $isGraphQLTestable = false;

    protected function getFixtures(): array
    {
        return [
            LoadCourseData::class,
        ];
    }

    public static function filtersToTest(): array
    {
        return [
            // 'id' => [[1], ['id' => 2013]], // skipped
            // 'ids' => [[0, 2], ['id' => [2012, 2016]]], // skipped
            // 'title' => [[1], ['id' => 2013]], // skipped
            // 'titles' => [[0, 2], ['id' => [2012, 2016]]], // skipped
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        return self::filtersToTest();
    }

    public function testPostIs404(): void
    {
        $this->fourOhFourTest('POST');
    }

    public function testPutIs404(): void
    {
        $academicYears = $this->getYears();
        $id = $academicYears[0]['id'];

        $this->fourOhFourTest('PUT', ['id' => $id]);
    }

    public function testDeleteIs404(): void
    {
        $academicYears = $this->getYears();
        $id = $academicYears[0]['id'];

        $this->fourOhFourTest('DELETE', ['id' => $id]);
    }

    protected function fourOhFourTest(string $type, array $parameters = []): void
    {
        $url = '/api/' . $this->apiVersion . '/academicyears/';
        if (array_key_exists('id', $parameters)) {
            $url .= $parameters['id'];
        }
        $this->createJsonRequest(
            $type,
            $url,
            null,
            $this->createJwtForRootUser($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
    }

    protected function getYears(): array
    {
        $courses = $this->fixtures->getReferencesByClass()[Course::class];
        $academicYears = array_map(fn(Course $course) => $course->getYear(), $courses);
        $academicYears = array_unique($academicYears);
        sort($academicYears);
        return array_map(fn($year) => [
            'id' => $year,
            'title' => $year,
        ], $academicYears);
    }

    protected function anonymousAccessDeniedOneTest(): void
    {
        $academicYears = $this->getYears();
        $id = $academicYears[0]['id'];
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_academicyears_getone",
                ['version' => $this->apiVersion, 'id' => $id]
            ),
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    protected function anonymousAccessDeniedAllTest(): void
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_academicyears_getall",
                ['version' => $this->apiVersion]
            ),
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    protected function runGetAllTest(string $jwt): void
    {
        $this->getAllTest($jwt);
        $this->getAllJsonApiTest($jwt);
    }

    protected function getOneTest(string $jwt): array
    {
        $academicYears = $this->getYears();
        $data = $academicYears[0];
        $returnedData = $this->getOne('academicyears', 'academicYears', $data['id'], $jwt);
        $this->compareData($data, $returnedData);
        return $returnedData;
    }

    protected function getOneJsonApiTest(string $jwt): object
    {
        $academicYears = $this->getYears();
        $data = $academicYears[0];
        $returnedData = $this->getOneJsonApi('academicyears', (string) $data['id'], $jwt);
        $this->compareJsonApiData($data, $returnedData);
        return $returnedData;
    }

    protected function getAllTest(string $jwt): array
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $academicYears = $this->getYears();
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_{$endpoint}_getall",
                ['version' => $this->apiVersion]
            ),
            null,
            $jwt
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);

        $responses = json_decode($response->getContent(), true)[$responseKey];
        $this->assertEquals(
            $academicYears,
            $responses
        );

        return $responses;
    }

    protected function getAllJsonApiTest(string $jwt): array
    {
        $endpoint = $this->getPluralName();
        $academicYears = $this->getYears();
        $this->createJsonApiRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_{$endpoint}_getall",
                ['version' => $this->apiVersion]
            ),
            null,
            $jwt
        );
        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonApiResponse($response, Response::HTTP_OK);

        $content = json_decode($response->getContent());

        $this->assertCount(0, $content->included, var_export($content, true));
        $this->assertIsArray($content->data);

        foreach ($content->data as $i => $item) {
            $this->assertTrue(property_exists($item, 'id'));
            $this->assertTrue(property_exists($item, 'type'));
            $this->assertTrue(property_exists($item, 'attributes'));
            $this->assertTrue(property_exists($item, 'relationships'));

            $this->compareJsonApiData($academicYears[$i], $item);
        }

        return $content->data;
    }

    protected function runFiltersTest(string $jwt, array $dataKeys = [], array $filterParts = []): void
    {
        if (empty($filterParts)) {
            $this->markTestSkipped('Missing filters tests for this endpoint');
        }
        $all = $this->getYears();
        $expectedData = array_map(fn($i) => $all[$i], $dataKeys);
        $filters = [];
        foreach ($filterParts as $key => $value) {
            $filters["filters[$key]"] = $value;
        }
        $this->filterTest($filters, $expectedData, $jwt);
    }

    public function testFilters(array $dataKeys = [], array $filterParts = []): void
    {
        $this->markTestSkipped('test not applicable');
    }

    public function testFiltersWithServiceToken(array $dataKeys = [], array $filterParts = []): void
    {
        $this->markTestSkipped('test not applicable');
    }

    public function testGraphQLFilters(array $dataKeys = [], array $filterParts = [], bool $skipped = false): void
    {
        $this->markTestSkipped('test not applicable');
    }
}
