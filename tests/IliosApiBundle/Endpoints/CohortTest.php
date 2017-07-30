<?php

namespace Tests\IliosApiBundle\Endpoints;

use Symfony\Component\HttpFoundation\Response;
use Tests\CoreBundle\DataLoader\ProgramYearData;
use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * Cohort API endpoint Test.
 * @group api_2
 */
class CohortTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'cohorts';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadCohortData',
            'Tests\CoreBundle\Fixture\LoadProgramYearData',
            'Tests\CoreBundle\Fixture\LoadProgramYearStewardData',
            'Tests\CoreBundle\Fixture\LoadCourseData',
            'Tests\CoreBundle\Fixture\LoadLearnerGroupData',
            'Tests\CoreBundle\Fixture\LoadUserData'
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text(60)],
            'courses' => ['courses', [1]],
            'learnerGroups' => ['learnerGroups', [1], $skipped = true],
            'users' => ['users', [1]],
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
            'title' => [[1], ['title' => 'Class of 2018']],
            'programYear' => [[1], ['programYear' => 2]],
            'courses' => [[2], ['courses' => [4]]],
            'learnerGroups' => [[1], ['learnerGroups' => [2]]],
            'users' => [[0], ['users' => [2]]],
            'schools' => [[3], ['schools' => [2]]],
            'startYears' => [[1, 3], ['startYears' => ['2014', '2016']]],
        ];
    }

    public function testPostOne()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('ilios_api_post', ['version' => 'v1', 'object' => 'cohorts']),
            json_encode(['cohort' => $data]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_GONE);
    }

    public function testCreateWithPut()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $this->createJsonRequest(
            'PUT',
            $this->getUrl('ilios_api_put', [
                'version' => 'v1',
                'object' => 'cohorts',
                'id' => $data['id']
            ]),
            json_encode(['cohort' => $data]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_GONE);
    }

    public function testDelete()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $this->createJsonRequest(
            'DELETE',
            $this->getUrl('ilios_api_delete', ['version' => 'v1', 'object' => 'cohorts', 'id' => $data['id']]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_GONE);
    }

    /**
     * This test is disabled since cohorts can't be posted
     */
    public function testPostBad()
    {
        $this->assertTrue(true);
    }

    /**
     * This test is disabled since cohorts can't be posted
     */
    public function testPostMany()
    {
        $this->assertTrue(true);
    }

    /**
     * Unlock program years before attempting to PUT cohorts
     */
    public function testPutForAllData()
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
            $this->putOne('programyears', 'programYear', $programYearId, $programYear);
            $cohort[$changeKey] = $changeValue;
            $this->putTest($cohort, $cohort, $cohort['id']);
        }
    }

    public function testRejectPutCohortInLockedProgramYear()
    {
        $userId = 2;
        $dataLoader = $this->getDataLoader();
        $cohort = $dataLoader->getOne();
        $programYear = $this->getProgramYear($cohort['programYear']);
        $programYear['locked'] = true;
        $programYear['archived'] = false;
        $this->putOne('programyears', 'programYear', $programYear['id'], $programYear);

        $id = $cohort['id'];

        $this->canNot(
            $userId,
            'PUT',
            $this->getUrl('ilios_api_put', ['version' => 'v1', 'object' => 'cohorts', 'id' => $id]),
            json_encode(['cohort' => $cohort])
        );
    }

    public function testRejectPutCohortInArchivedProgramYear()
    {
        $userId = 2;
        $dataLoader = $this->getDataLoader();
        $cohort = $dataLoader->getOne();
        $programYear = $this->getProgramYear($cohort['programYear']);
        $programYear['locked'] = false;
        $programYear['archived'] = true;
        $this->putOne('programyears', 'programYear', $programYear['id'], $programYear);

        $id = $cohort['id'];

        $this->canNot(
            $userId,
            'PUT',
            $this->getUrl('ilios_api_put', ['version' => 'v1', 'object' => 'cohorts', 'id' => $id]),
            json_encode(['cohort' => $cohort])
        );
    }

    /**
     * Get programYear data from loader by id
     * @param integer $programYearId
     *
     * @return array
     */
    protected function getProgramYear($id)
    {
        $programYearDataLoader = $this->container->get(ProgramYearData::class);
        $allProgramYears = $programYearDataLoader->getAll();
        $programYearsById = [];
        foreach ($allProgramYears as $arr) {
            $programYearsById[$arr['id']] = $arr;
        }

        return $programYearsById[$id];
    }
}
