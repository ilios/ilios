<?php

namespace App\Tests\Endpoints;

use Symfony\Component\HttpFoundation\Response;
use App\Tests\DataLoader\ObjectiveData;
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
            'App\Tests\Fixture\LoadObjectiveData',
            'App\Tests\Fixture\LoadProgramYearStewardData',
            'App\Tests\Fixture\LoadSessionData',
            'App\Tests\Fixture\LoadCourseData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'startYear' => ['startYear', $this->getFaker()->randomDigitNotNull],
            'locked' => ['locked', true],
            'archived' => ['archived', true],
            'publishedAsTbd' => ['publishedAsTbd', true],
            'published' => ['published', false],
            'program' => ['program', 2],
            'cohort' => ['cohort', 2, $skipped = true],
            'directors' => ['directors', [2]],
            'competencies' => ['competencies', [2]],
            'terms' => ['terms', [2]],
            'objectives' => ['objectives', [2]],
            'stewards' => ['stewards', [2], $skipped = true],
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
            'publishedAsTbd' => [[1], ['publishedAsTbd' => true]],
            'notPublishedAsTbd' => [[0, 2, 3, 4], ['publishedAsTbd' => false]],
            'published' => [[0, 1, 3, 4], ['published' => true]],
            'notPublished' => [[2], ['published' => false]],
            'program' => [[3], ['program' => 3]],
            'cohort' => [[1], ['cohort' => 2], $skipped = true],
            'directors' => [[0], ['directors' => [1]], $skipped = true],
            'competencies' => [[0], ['competencies' => [1]], $skipped = true],
            'terms' => [[1], ['terms' => [1]]],
            'objectives' => [[0], ['objectives' => [1]], $skipped = true],
            'stewards' => [[0], ['stewards' => [1]], $skipped = true],
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
        $this->assertEquals($cohort['title'], 'Class of '.($fetchedResponseData['startYear'] + $program['duration']));

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

        usort(
            $fetchedResponseData,
            function ($a, $b) {
                return strnatcasecmp($a['id'], $b['id']);
            }
        );

        $program = $this->getOne('programs', 'programs', $data[0]['program']);
        foreach ($data as $i => $datum) {
            $response = $fetchedResponseData[$i];

            $cohortId = $response['cohort'];
            $this->assertNotEmpty($cohortId);
            unset($response['cohort']);
            $this->compareData($datum, $response);

            $cohort = $this->getOne('cohorts', 'cohorts', $cohortId);
            $this->assertEquals($cohort['programYear'], $response['id']);
            $this->assertEquals($cohort['title'], 'Class of '.($response['startYear'] + $program['duration']));
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
            $userId,
            'POST',
            $this->getUrl('ilios_api_post', ['version' => 'v1', 'object' => 'programyears']),
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
            $userId,
            'PUT',
            $this->getUrl('ilios_api_put', ['version' => 'v1', 'object' => 'programyears', 'id' => $data['id']]),
            json_encode(['programYear' => $data])
        );
    }

    public function testRejectUnprivilegedDelete()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $userId = 3;

        $this->canNot(
            $userId,
            'DELETE',
            $this->getUrl('ilios_api_delete', ['version' => 'v1', 'object' => 'programyears', 'id' => $data['id']])
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

    public function testRemoveLinksFromOrphanedObjectives()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $id = $data['id'];
        $self = $this;

        //create data we an depend on
        $dataLoader = $this->getContainer()->get(ObjectiveData::class);
        $create = [];
        for ($i = 0; $i < 2; $i++) {
            $arr = $dataLoader->create();
            $arr['parents'] = ['1'];
            $arr['children'] = ['7', '8'];
            $arr['competency'] = 1;
            $arr['programYears'] = [$id];
            $arr['courses'] = [];
            $arr['sessions'] = [];
            unset($arr['id']);
            $create[] = $arr;
        }
        $newObjectives = $this->postMany('objectives', 'objectives', $create);

        $getObjectives = function ($id) use ($self) {
            return $self->getOne('objectives', 'objectives', $id);
        };
        $objectives = array_map($getObjectives, array_column($newObjectives, 'id'));
        foreach ($objectives as $arr) {
            $this->assertNotEmpty($arr['parents'], 'parents have been created');
            $this->assertNotEmpty($arr['children'], 'children have been created');
            $this->assertArrayHasKey('competency', $arr);
        }
        $this->deleteTest($id);
        $objectives = array_map($getObjectives, array_column($newObjectives, 'id'));
        foreach ($objectives as $arr) {
            $this->assertEmpty($arr['parents'], 'parents have been removed');
            $this->assertEmpty($arr['children'], 'children have been removed');
            $this->assertArrayNotHasKey('competency', $arr);
        }
    }

    /**
     * @covers \App\Controller\ProgramYearController::downloadCourseObjectivesReportAction
     */
    public function testDownloadCourseObjectivesReport()
    {
        $parameters = array_merge(
            [
                'version' => 'v1',
                'object' => $this->getPluralName(),
                'id' => 1,
            ]
        );

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'ilios_api_programyears_downloadobjectivesmapping',
                $parameters
            ),
            null,
            $this->getTokenForUser(2)
        );

        $response = self::$client->getResponse();

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
            ['Miss', '2013 - 2014', 'first objective', 'third competency', 'firstCourse', 'first', 'second objective',],
            ['Miss', '2013 - 2014', 'first objective', 'third competency', 'course 2', 'second', 'second objective',],
            [
                'Miss',
                '2013 - 2014',
                'first objective',
                'third competency',
                'fourth course',
                'fourth',
                'second objective',
            ],
            ['Miss', '2013 - 2014', 'second objective', null, null, null, 'third objective',],
            ['Miss', '2013 - 2014', 'second objective', null, null, null, 'sixth objective',],
        ];

        $actual = array_map('str_getcsv', explode(PHP_EOL, trim($response->getContent())));
        $this->assertEquals($response->getStatusCode(), Response::HTTP_OK);
        $this->assertStringStartsWith('text/csv', $response->headers->get('Content-Type'));
        $this->assertEquals($expected, $actual);
    }
}
