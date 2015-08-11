<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * Cohort controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class CohortControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadCohortData',
            'Ilios\CoreBundle\Tests\Fixture\LoadProgramYearData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseData',
            'Ilios\CoreBundle\Tests\Fixture\LoadLearnerGroupData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData'
        ]);
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
        ];
    }

    public function testGetCohort()
    {
        $cohort = $this->container
            ->get('ilioscore.dataloader.cohort')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_cohorts',
                ['id' => $cohort['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($cohort),
            json_decode($response->getContent(), true)['cohorts'][0]
        );
    }

    public function testGetAllCohorts()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_cohorts'), null, $this->getAuthenticatedUserToken());
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.cohort')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['cohorts']
        );
    }

    public function testPostCohort()
    {
        //create a program year we can attach this cohort to
        $data = $this->container->get('ilioscore.dataloader.programYear')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_programyears'),
            json_encode(['programYear' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $newProgramYearId = json_decode($response->getContent(), true)['programYears'][0]['id'];

        $data = $this->container->get('ilioscore.dataloader.cohort')
            ->create();
        $data['programYear'] = $newProgramYearId;
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_cohorts'),
            json_encode(['cohort' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['cohorts'][0],
            $response->getContent()
        );
    }

    public function testPostBadCohort()
    {
        $invalidCohort = $this->container
            ->get('ilioscore.dataloader.cohort')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_cohorts'),
            json_encode(['cohort' => $invalidCohort]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPutCohort()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.cohort')
            ->getOne();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_cohorts',
                ['id' => $data['id']]
            ),
            json_encode(['cohort' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['cohort']
        );
    }

    public function testDeleteCohort()
    {
        $cohort = $this->container
            ->get('ilioscore.dataloader.cohort')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_cohorts',
                ['id' => $cohort['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_cohorts',
                ['id' => $cohort['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testCohortNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_cohorts', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
