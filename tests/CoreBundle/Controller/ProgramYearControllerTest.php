<?php

namespace Tests\CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * ProgramYear controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class ProgramYearControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadProgramYearData',
            'Ilios\CoreBundle\Tests\Fixture\LoadProgramData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCohortData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCompetencyData',
            'Ilios\CoreBundle\Tests\Fixture\LoadTermData',
            'Ilios\CoreBundle\Tests\Fixture\LoadObjectiveData',
            'Ilios\CoreBundle\Tests\Fixture\LoadProgramYearStewardData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseData'
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
     * @group controllers_b
     */
    public function testGetProgramYear()
    {
        $programYear = $this->container
            ->get('ilioscore.dataloader.programyear')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_programyears',
                ['id' => $programYear['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($programYear),
            json_decode($response->getContent(), true)['programYears'][0]
        );
    }

    /**
     * @group controllers_b
     */
    public function testGetAllProgramYears()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_programyears'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.programyear')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['programYears']
        );
    }

    /**
     * @group controllers_b
     */
    public function testPostProgramYear()
    {
        $data = $this->container->get('ilioscore.dataloader.programyear')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['stewards']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_programyears'),
            json_encode(['programYear' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['programYears'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers_b
     */
    public function testPostBadProgramYear()
    {
        $invalidProgramYear = $this->container
            ->get('ilioscore.dataloader.programyear')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_programyears'),
            json_encode(['programYear' => $invalidProgramYear]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers_b
     */
    public function testPutProgramYear()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.programyear')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['stewards']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_programyears',
                ['id' => $data['id']]
            ),
            json_encode(['programYear' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['programYear']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteProgramYear()
    {
        $programYear = $this->container
            ->get('ilioscore.dataloader.programyear')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_programyears',
                ['id' => $programYear['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_programyears',
                ['id' => $programYear['id']]
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
    public function testProgramYearNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_programyears', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }

    /**
     * @group controllers
     */
    public function testFilterByCourses()
    {
        $programYears = $this->container->get('ilioscore.dataloader.programyear')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_programyears', ['filters[courses]' => 4]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['programYears'];
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $programYears[2]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterBySession()
    {
        $programYears = $this->container->get('ilioscore.dataloader.programyear')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_programyears', ['filters[sessions][]' => 3]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['programYears'];
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $programYears[0]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByTerms()
    {
        $programYears = $this->container->get('ilioscore.dataloader.programyear')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_programyears', ['filters[terms][]' => 1]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['programYears'];
        $this->assertEquals(1, count($data));
        $this->assertEquals(
            $this->mockSerialize(
                $programYears[1]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterBySchool()
    {
        $programYears = $this->container->get('ilioscore.dataloader.programyear')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_programyears', ['filters[schools][]' => 1]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['programYears'];
        $this->assertEquals(3, count($data));
        $this->assertEquals(
            $this->mockSerialize(
                $programYears[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $programYears[1]
            ),
            $data[1]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $programYears[2]
            ),
            $data[2]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByStartYear()
    {
        $programYears = $this->container->get('ilioscore.dataloader.programyear')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_programyears', ['filters[startYear]' => '2014']),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['programYears'];
        $this->assertEquals(1, count($data));
        $this->assertEquals(
            $this->mockSerialize(
                $programYears[1]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByStartYears()
    {
        $programYears = $this->container->get('ilioscore.dataloader.programyear')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_programyears', ['filters[startYears][]' => ['2014', '2015']]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['programYears'];
        $this->assertEquals(2, count($data));
        $this->assertEquals(
            $this->mockSerialize(
                $programYears[1]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $programYears[2]
            ),
            $data[1]
        );
    }
}
