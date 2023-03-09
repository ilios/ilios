<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadCourseData;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\DataLoader\CourseData;
use App\Tests\ReadEndpointTest;

/**
 * AamcMethod API endpoint Test.
 * @group api_5
 */
class AcademicYearTest extends ReadEndpointTest
{
    protected string $testName = 'academicYears';

    protected function getFixtures(): array
    {
        return [
            LoadCourseData::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function filtersToTest(): array
    {
        return [
            'id' => [[1], ['id' => 2013], $skipped = true],
            'ids' => [[0, 2], ['id' => [2012, 2016]], $skipped = true],
            'title' => [[1], ['id' => 2013], $skipped = true],
            'titles' => [[0, 2], ['id' => [2012, 2016]], $skipped = true],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        return self::filtersToTest();
    }

    public function testGetOne()
    {
        $academicYears = $this->getYears();
        $data = $academicYears[0];
        $returnedData = $this->getOne('academicyears', 'academicYears', $data['id']);
        $this->compareData($data, $returnedData);
    }

    public function testGetOneJsonApi()
    {
        $academicYears = $this->getYears();
        $data = $academicYears[0];
        $returnedData = $this->getOneJsonApi('academicyears', (string) $data['id']);
        $this->compareJsonApiData($data, $returnedData);
    }

    public function testGetAll()
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
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
        $responses = json_decode($response->getContent(), true)[$responseKey];


        $this->assertEquals(
            $academicYears,
            $responses
        );
    }

    public function testPostIs404()
    {
        $this->fourOhFourTest('POST');
    }

    public function testPutIs404()
    {
        $academicYears = $this->getYears();
        $id = $academicYears[0]['id'];

        $this->fourOhFourTest('PUT', ['id' => $id]);
    }

    public function testDeleteIs404()
    {
        $academicYears = $this->getYears();
        $id = $academicYears[0]['id'];

        $this->fourOhFourTest('DELETE', ['id' => $id]);
    }

    protected function fourOhFourTest($type, array $parameters = [])
    {
        $url = '/api/' . $this->apiVersion . '/academicyears/';
        if (array_key_exists('id', $parameters)) {
            $url .= $parameters['id'];
        }
        $this->createJsonRequest(
            $type,
            $url,
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
    }

    protected function getYears()
    {
        $loader = self::getContainer()->get(CourseData::class);
        $data = $loader->getAll();
        $academicYears = array_map(fn($arr) => $arr['year'], $data);
        $academicYears = array_unique($academicYears);
        sort($academicYears);
        $academicYears = array_map(fn($year) => [
            'id' => $year,
            'title' => $year
        ], $academicYears);

        return $academicYears;
    }

    public function anonymousAccessDeniedOneTest()
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
    public function anonymousAccessDeniedAllTest()
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
}
