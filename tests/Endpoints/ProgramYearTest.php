<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use Symfony\Component\HttpFoundation\Response;
use App\Tests\ReadWriteEndpointTest;

/**
 * ProgramYear API endpoint Test.
 * @group api_3
 */
class ProgramYearTest extends ReadWriteEndpointTest
{
    protected $testName = 'programYears';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadProgramYearData',
            'App\Tests\Fixture\LoadProgramData',
            'App\Tests\Fixture\LoadCohortData',
            'App\Tests\Fixture\LoadUserData',
            'App\Tests\Fixture\LoadCompetencyData',
            'App\Tests\Fixture\LoadTermData',
            'App\Tests\Fixture\LoadSessionData',
            'App\Tests\Fixture\LoadCourseData',
            'App\Tests\Fixture\LoadProgramYearObjectiveData',
            'App\Tests\Fixture\LoadCourseObjectiveData',
            'App\Tests\Fixture\LoadSessionObjectiveData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'startYear' => ['startYear', $this->getFaker()->randomDigitNotNull()],
            'locked' => ['locked', true],
            'archived' => ['archived', true],
            'program' => ['program', 2],
            'cohort' => ['cohort', 2, $skipped = true],
            'directors' => ['directors', [2]],
            'competencies' => ['competencies', [2]],
            'terms' => ['terms', [2]],
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
            'locked' => [[3], ['locked' => true]],
            'notLocked' => [[0, 1, 2, 4], ['locked' => false]],
            'archived' => [[2], ['archived' => true]],
            'notArchived' => [[0, 1, 3, 4], ['archived' => false]],
            'program' => [[3], ['program' => 3]],
            'cohort' => [[1], ['cohort' => 2], $skipped = true],
            'directors' => [[0], ['directors' => [1]], $skipped = true],
            'competencies' => [[0], ['competencies' => [1]], $skipped = true],
            'terms' => [[1], ['terms' => [1]]],
            'courses' => [[2], ['courses' => [4]]],
            'sessions' => [[0], ['sessions' => [3]]],
            'schools' => [[0, 1, 2, 4], ['schools' => [1]]],
            'startYear' => [[1], ['startYear' => ['2014']]],
            'startYears' => [[1, 2], ['startYears' => ['2014', '2015']]],
        ];
    }

    protected function postTest(array $data, array $postData)
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $postKey = $this->getCamelCasedSingularName();
        $responseData = $this->postOne($endpoint, $postKey, $responseKey, $postData);

        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getOne($endpoint, $responseKey, $responseData['id']);

        $cohortId = $fetchedResponseData['cohort'];
        $this->assertNotEmpty($cohortId);
        unset($fetchedResponseData['cohort']);
        $this->compareData($data, $fetchedResponseData);

        $cohort = $this->getOne('cohorts', 'cohorts', $cohortId);
        $program = $this->getOne('programs', 'programs', $fetchedResponseData['program']);

        $this->assertEquals($cohort['programYear'], $fetchedResponseData['id']);
        $this->assertEquals($cohort['title'], 'Class of ' . ($fetchedResponseData['startYear'] + $program['duration']));

        return $fetchedResponseData;
    }

    /**
     * Test saving new data to the JSON:API
     * @return mixed
     */
    protected function postJsonApiTest(object $postData, array $data)
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $responseData = $this->postOneJsonApi($postData);

        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getOne($endpoint, $responseKey, $responseData->id);

        $cohortId = $fetchedResponseData['cohort'];
        $this->assertNotEmpty($cohortId);
        unset($fetchedResponseData['cohort']);
        $this->compareData($data, $fetchedResponseData);

        $cohort = $this->getOne('cohorts', 'cohorts', $cohortId);
        $program = $this->getOne('programs', 'programs', $fetchedResponseData['program']);

        $this->assertEquals($cohort['programYear'], $fetchedResponseData['id']);
        $this->assertEquals($cohort['title'], 'Class of ' . ($fetchedResponseData['startYear'] + $program['duration']));

        return $fetchedResponseData;
    }


    protected function postManyTest(array $data)
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $responseData = $this->postMany($endpoint, $responseKey, $data);
        $ids = array_map(
            function (array $arr) {
                return $arr['id'];
            },
            $responseData
        );
        $filters = [
            'filters[id]' => $ids,
            'limit' => count($ids),
        ];
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getFiltered($endpoint, $responseKey, $filters);

        usort($fetchedResponseData, function ($a, $b) {
            return $a['id'] <=> $b['id'];
        });

        $program = $this->getOne('programs', 'programs', $data[0]['program']);
        foreach ($data as $i => $datum) {
            $response = $fetchedResponseData[$i];

            $cohortId = $response['cohort'];
            $this->assertNotEmpty($cohortId);
            unset($response['cohort']);
            $this->compareData($datum, $response);

            $cohort = $this->getOne('cohorts', 'cohorts', $cohortId);
            $this->assertEquals($cohort['programYear'], $response['id']);
            $this->assertEquals($cohort['title'], 'Class of ' . ($response['startYear'] + $program['duration']));
        }

        return $fetchedResponseData;
    }

    protected function postManyJsonApiTest(object $postData, array $data)
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $responseData = $this->postManyJsonApi($postData);
        $ids = array_column($responseData, 'id');
        $filters = [
            'filters[id]' => $ids,
            'limit' => count($ids),
        ];
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getFiltered($endpoint, $responseKey, $filters);

        usort($fetchedResponseData, function ($a, $b) {
            return $a['id'] <=> $b['id'];
        });

        $program = $this->getOne('programs', 'programs', $data[0]['program']);
        foreach ($data as $i => $datum) {
            $response = $fetchedResponseData[$i];

            $cohortId = $response['cohort'];
            $this->assertNotEmpty($cohortId);
            unset($response['cohort']);
            $this->compareData($datum, $response);

            $cohort = $this->getOne('cohorts', 'cohorts', $cohortId);
            $this->assertEquals($cohort['programYear'], $response['id']);
            $this->assertEquals($cohort['title'], 'Class of ' . ($response['startYear'] + $program['duration']));
        }

        return $fetchedResponseData;
    }

    /**
     * Delete ProgramYear 3 explicitly as ProgramYear 1 is linked
     * to Program 1.  Since sqlite doesn't cascade this doesn't work
     * @inheritdoc
     */
    public function testDelete()
    {
        $this->deleteTest(3);
    }

    public function testRejectUnprivilegedPost()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $userId = 3;

        $this->canNot(
            $this->kernelBrowser,
            $userId,
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_programyears_post',
                ['version' => $this->apiVersion]
            ),
            json_encode(['programYears' => [$data]])
        );
    }

    public function testPostWithNullCohort()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $dataLoader->create();
        $postData['cohort'] = null;
        $this->postTest($data, $postData);
    }

    public function testRejectUnprivilegedPut()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $userId = 3;

        $this->canNot(
            $this->kernelBrowser,
            $userId,
            'PUT',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_programyears_put',
                ['version' => $this->apiVersion, 'id' => $data['id']]
            ),
            json_encode(['programYear' => $data])
        );
    }

    public function testRejectUnprivilegedDelete()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $userId = 3;

        $this->canNot(
            $this->kernelBrowser,
            $userId,
            'DELETE',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_programyears_delete',
                ['version' => $this->apiVersion, 'id' => $data['id']]
            )
        );
    }

    public function testProgramYearCanBeUnlocked()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        //lock programYear
        $data['locked'] = true;
        $response = $this->putOne('programyears', 'programYear', $data['id'], $data);

        $this->assertTrue($response['locked']);

        //unlock programYear
        $data['locked'] = false;
        $response = $this->putOne('programyears', 'programYear', $data['id'], $data);
        $this->assertFalse($response['locked']);
    }

    /**
     * @covers \App\Controller\ProgramYearController::downloadCourseObjectivesReportAction
     */
    public function testDownloadCourseObjectivesReport()
    {
        $parameters = array_merge(
            [
                'version' => $this->apiVersion,
                'object' => $this->getPluralName(),
                'id' => 1,
            ]
        );

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_programyears_downloadcourseobjectivesreport',
                $parameters
            ),
            null,
            $this->getTokenForUser($this->kernelBrowser, 2)
        );

        $response = $this->kernelBrowser->getResponse();

        $expected = [
            [
                'program_title',
                'matriculation_year',
                'program_year_objective',
                'competency',
                'course_title',
                'course_shortname',
                'mapped_course_objective',
            ],
            [
                'Miss',
                '2013 - 2014',
                'program year objective 1',
                'first competency',
                'firstCourse',
                'first',
                'course objective 1'
            ]
        ];

        $actual = array_map('str_getcsv', explode(PHP_EOL, trim($response->getContent())));
        $this->assertEquals($response->getStatusCode(), Response::HTTP_OK);
        $this->assertStringStartsWith('text/csv', $response->headers->get('Content-Type'));
        $this->assertEquals($expected, $actual);
    }
}
