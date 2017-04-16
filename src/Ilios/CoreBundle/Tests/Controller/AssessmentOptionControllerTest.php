<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * AssessmentOption controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class AssessmentOptionControllerTest extends AbstractControllerTest
{
    /**
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadAssessmentOptionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionTypeData'
        ];
    }

    public function testGetAssessmentOption()
    {
        $assessmentOption = $this->container
            ->get('ilioscore.dataloader.assessmentoption')
            ->getOne()['assessmentOption']
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_assessmentoptions',
                ['id' => $assessmentOption['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $assessmentOption,
            json_decode($response->getContent(), true)['assessmentOption']
        );
    }

    public function testGetAllAssessmentOptions()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_assessmentoptions'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.assessmentoption')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostAssessmentOption()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_assessmentoptions'),
            json_encode(
                $this->container->get('ilioscore.dataloader.assessmentoption')
                    ->create()['assessmentOption']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadAssessmentOption()
    {
        $invalidAssessmentOption = array_shift(
            $this->container->get('ilioscore.dataloader.assessmentoption')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_assessmentoptions'),
            $invalidAssessmentOption
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutAssessmentOption()
    {
        $assessmentOption = $this->container
            ->get('ilioscore.dataloader.assessmentoption')
            ->createWithId()['assessmentOption']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_assessmentoptions',
                ['id' => $assessmentOption['id']]
            ),
            json_encode($assessmentOption)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.assessmentoption')
                ->getLastCreated()['assessmentOption'],
            json_decode($response->getContent(), true)['assessmentOption']
        );
    }

    public function testDeleteAssessmentOption()
    {
        $assessmentOption = $this->container
            ->get('ilioscore.dataloader.assessmentoption')
            ->createWithId()['assessmentOption']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_assessmentoptions',
                ['id' => $assessmentOption['id']]
            ),
            json_encode($assessmentOption)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_assessmentoptions',
                ['id' => $assessmentOption['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_assessmentoptions',
                ['id' => $assessmentOption['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testAssessmentOptionNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_assessmentoptions', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
