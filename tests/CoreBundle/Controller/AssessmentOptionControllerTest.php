<?php

namespace Tests\CoreBundle\Controller;

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
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Tests\CoreBundle\Fixture\LoadAssessmentOptionData',
            'Tests\CoreBundle\Fixture\LoadSessionTypeData'
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

    /**
     * @group controllers_a
     */
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
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($assessmentOption),
            json_decode($response->getContent(), true)['assessmentOptions'][0]
        );
    }

    /**
     * @group controllers_a
     */
    public function testGetAllAssessmentOptions()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_assessmentoptions'),
            null,
            $this->getAuthenticatedUserToken()
        );
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

    /**
     * @group controllers_a
     */
    public function testPostAssessmentOption()
    {
        $data = $this->container->get('ilioscore.dataloader.assessmentoption')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_assessmentoptions'),
            json_encode(['assessmentOption' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['assessmentOptions'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostBadAssessmentOption()
    {
        $invalidAssessmentOption = $this->container
            ->get('ilioscore.dataloader.assessmentoption')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_assessmentoptions'),
            json_encode(['assessmentOption' => $invalidAssessmentOption]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers_a
     */
    public function testPutAssessmentOption()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.assessmentoption')
            ->getOne();
        $data['sessionTypes'] = ['2'];

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_assessmentoptions',
                ['id' => $data['id']]
            ),
            json_encode(['assessmentOption' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['assessmentOption']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteAssessmentOption()
    {
        $assessmentOption = $this->container
            ->get('ilioscore.dataloader.assessmentoption')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_assessmentoptions',
                ['id' => $assessmentOption['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_assessmentoptions',
                ['id' => $assessmentOption['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testAssessmentOptionNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_assessmentoptions', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
