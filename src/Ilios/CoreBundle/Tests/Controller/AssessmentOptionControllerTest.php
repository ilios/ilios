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
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadAssessmentOptionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionTypeData'
        ];
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'name'
        ];
    }

    public function testGetAssessmentOption()
    {
        $assessmentOption = $this->container
            ->get('ilioscore.dataloader.assessmentoption')
            ->getOne()
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
            $this->mockSerialize($assessmentOption),
            json_decode($response->getContent(), true)['assessmentOptions'][0]
        );
    }

    public function testGetAllAssessmentOptions()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_assessmentoptions'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.assessmentoption')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['assessmentOptions']
        );
    }

    public function testPostAssessmentOption()
    {
        $data = $this->container->get('ilioscore.dataloader.assessmentoption')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_assessmentoptions'),
            json_encode(['assessmentOption' => $data])
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

    public function testPostBadAssessmentOption()
    {
        $invalidAssessmentOption = $this->container
            ->get('ilioscore.dataloader.assessmentoption')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_assessmentoptions'),
            json_encode(['assessmentOption' => $invalidAssessmentOption])
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutAssessmentOption()
    {
        $assessmentOption = $this->container
            ->get('ilioscore.dataloader.assessmentoption')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_assessmentoptions',
                ['id' => $assessmentOption['id']]
            ),
            json_encode(['assessmentOption' => $assessmentOption])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($assessmentOption),
            json_decode($response->getContent(), true)['assessmentOption']
        );
    }

    public function testDeleteAssessmentOption()
    {
        $assessmentOption = $this->container
            ->get('ilioscore.dataloader.assessmentoption')
            ->getOne()
        ;

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
            $this->getUrl('get_assessmentoptions', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
