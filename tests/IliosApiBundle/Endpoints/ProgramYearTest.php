<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * ProgramYear API endpoint Test.
 * @group api_3
 */
class ProgramYearTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'programYears';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadProgramYearData',
            'Tests\CoreBundle\Fixture\LoadProgramData',
            'Tests\CoreBundle\Fixture\LoadCohortData',
            'Tests\CoreBundle\Fixture\LoadUserData',
            'Tests\CoreBundle\Fixture\LoadCompetencyData',
            'Tests\CoreBundle\Fixture\LoadTermData',
            'Tests\CoreBundle\Fixture\LoadObjectiveData',
            'Tests\CoreBundle\Fixture\LoadProgramYearStewardData',
            'Tests\CoreBundle\Fixture\LoadSessionData',
            'Tests\CoreBundle\Fixture\LoadCourseData'
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
            'notLocked' => [[0, 1, 2], ['locked' => false]],
            'archived' => [[2], ['archived' => true]],
            'notArchived' => [[0, 1, 3], ['archived' => false]],
            'publishedAsTbd' => [[1], ['publishedAsTbd' => true]],
            'notPublishedAsTbd' => [[0, 2, 3], ['publishedAsTbd' => false]],
            'published' => [[0, 1, 3], ['published' => true]],
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
            'schools' => [[0, 1, 2], ['schools' => [1]]],
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

    protected function postManyTest(array $data)
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $responseData = $this->postMany($endpoint, $responseKey, $data);
        $ids = array_map(function (array $arr) {
            return $arr['id'];
        }, $responseData);
        $filters = [
            'filters[id]' => $ids,
            'limit' => count($ids)
        ];
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getFiltered($endpoint, $responseKey, $filters);

        usort($fetchedResponseData, function ($a, $b) {
            return strnatcasecmp($a['id'], $b['id']);
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
            $userId,
            'POST',
            $this->getUrl('ilios_api_post', ['version' => 'v1', 'object' => 'programyears']),
            json_encode(['programYears' => [$data]])
        );
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

    public function testProgramYearCannotBeUnlockedByNonDeveloper()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $programYearId = $data['id'];
        $data['locked'] = true;
        $responseData = $this->putTest($data, $data, $programYearId);
        $this->assertTrue(
            $responseData['locked']
        );

        $userId = 3;
        $user3 = $this->getOne('users', 'users', $userId);
        $this->assertNotContains(1, $user3['roles'], 'User #3 should not be a developer or this test is garbage.');
        //make User #3 a Course director
        $user3['roles'][] = 3;
        $this->putOne('users', 'user', $userId, $user3);

        $data['locked'] = false;
        $this->putOne('programyears', 'programYear', $programYearId, $data, false, $userId);
        $responseData = $this->getOne('programyears', 'programYears', $programYearId);
        $this->assertTrue(
            $responseData['locked'],
            'ProgramYear is still locked'
        );
    }
}
