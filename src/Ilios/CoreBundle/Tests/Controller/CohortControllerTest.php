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
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadCohortData',
            'Ilios\CoreBundle\Tests\Fixture\LoadProgramYearData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseData',
            'Ilios\CoreBundle\Tests\Fixture\LoadLearnerGroupData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData'
        ];
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
            )
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
        $this->createJsonRequest('GET', $this->getUrl('cget_cohorts'));
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
        $data = $this->container->get('ilioscore.dataloader.cohort')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_cohorts'),
            json_encode(['cohort' => $data])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());
        $this->assertTrue(
            $response->headers->contains(
                'Location'
            ),
            print_r($response->headers, true)
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
            json_encode(['cohort' => $invalidCohort])
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutCohort()
    {
        $cohort = $this->container
            ->get('ilioscore.dataloader.cohort')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_cohorts',
                ['id' => $cohort['id']]
            ),
            json_encode(['cohort' => $cohort])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($cohort),
            json_decode($response->getContent(), true)['cohort']
        );
    }

    public function testDeleteCohort()
    {
        $cohort = $this->container
            ->get('ilioscore.dataloader.cohort')
            ->getOne()
        ;

        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_cohorts',
                ['id' => $cohort['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_cohorts',
                ['id' => $cohort['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testCohortNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_cohorts', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
