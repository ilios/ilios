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
     * @return array
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

    public function testGetCohort()
    {
        $cohort = $this->container
            ->get('ilioscore.dataloader.cohort')
            ->getOne()['cohort']
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
            $cohort,
            json_decode($response->getContent(), true)['cohort']
        );
    }

    public function testGetAllCohorts()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_cohorts'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.cohort')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostCohort()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_cohorts'),
            json_encode(
                $this->container->get('ilioscore.dataloader.cohort')
                    ->create()['cohort']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadCohort()
    {
        $invalidCohort = array_shift(
            $this->container->get('ilioscore.dataloader.cohort')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_cohorts'),
            $invalidCohort
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutCohort()
    {
        $cohort = $this->container
            ->get('ilioscore.dataloader.cohort')
            ->createWithId()['cohort']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_cohorts',
                ['id' => $cohort['id']]
            ),
            json_encode($cohort)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.cohort')
                ->getLastCreated()['cohort'],
            json_decode($response->getContent(), true)['cohort']
        );
    }

    public function testDeleteCohort()
    {
        $cohort = $this->container
            ->get('ilioscore.dataloader.cohort')
            ->createWithId()['cohort']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_cohorts',
                ['id' => $cohort['id']]
            ),
            json_encode($cohort)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
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
            $this->getUrl('get_cohorts', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
