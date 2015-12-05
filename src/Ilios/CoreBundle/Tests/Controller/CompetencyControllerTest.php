<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * Competency controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class CompetencyControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadSchoolData',
            'Ilios\CoreBundle\Tests\Fixture\LoadObjectiveData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCompetencyData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionTypeData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseData',
            'Ilios\CoreBundle\Tests\Fixture\LoadAamcPcrsData',
            'Ilios\CoreBundle\Tests\Fixture\LoadProgramYearData'
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
    public function testGetCompetency()
    {
        $competency = $this->container
            ->get('ilioscore.dataloader.competency')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_competencies',
                ['id' => $competency['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($competency),
            json_decode($response->getContent(), true)['competencies'][0]
        );
    }

    /**
     * @group controllers_a
     */
    public function testGetAllCompetencies()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_competencies'), null, $this->getAuthenticatedUserToken());
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.competency')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['competencies']
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostCompetency()
    {
        $data = $this->container->get('ilioscore.dataloader.competency')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['children']);
        unset($postData['objectives']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_competencies'),
            json_encode(['competency' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['competencies'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostCompetencyProgramYear()
    {
        $data = $this->container->get('ilioscore.dataloader.competency')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['children']);
        unset($postData['objectives']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_competencies'),
            json_encode(['competency' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['competencies'][0]['id'];
        foreach ($postData['programYears'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_programyears',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['programYears'][0];
            $this->assertTrue(in_array($newId, $data['competencies']));
        }
    }

    /**
     * @group controllers_a
     */
    public function testPostBadCompetency()
    {
        $invalidCompetency = $this->container
            ->get('ilioscore.dataloader.competency')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_competencies'),
            json_encode(['competency' => $invalidCompetency]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testPutCompetency()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.competency')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['children']);
        unset($postData['objectives']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_competencies',
                ['id' => $data['id']]
            ),
            json_encode(['competency' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['competency']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteCompetency()
    {
        $competency = $this->container
            ->get('ilioscore.dataloader.competency')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_competencies',
                ['id' => $competency['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_competencies',
                ['id' => $competency['id']]
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
    public function testCompetencyNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_competencies', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }

    /**
     * @group controllers
     */
    public function testFilterByTopic()
    {
        $competencies = $this->container->get('ilioscore.dataloader.competency')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_competencies', ['filters[topics]' => [1, 2, 3]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['competencies'];
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $competencies[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $competencies[1]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterBySession()
    {
        $competencies = $this->container->get('ilioscore.dataloader.competency')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_competencies', ['filters[sessions][]' => 1]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['competencies'];
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $competencies[0]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterBySessionType()
    {
        $competencies = $this->container->get('ilioscore.dataloader.competency')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_competencies', ['filters[sessionTypes][]' => 1]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['competencies'];
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $competencies[0]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByCourse()
    {
        $competencies = $this->container->get('ilioscore.dataloader.competency')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_competencies', ['filters[courses][]' => 1]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['competencies'];
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $competencies[0]
            ),
            $data[0]
        );
    }
}
