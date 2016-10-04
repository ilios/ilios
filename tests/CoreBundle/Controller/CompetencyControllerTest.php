<?php

namespace Tests\CoreBundle\Controller;

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
            'Tests\CoreBundle\Fixture\LoadSchoolData',
            'Tests\CoreBundle\Fixture\LoadTermData',
            'Tests\CoreBundle\Fixture\LoadObjectiveData',
            'Tests\CoreBundle\Fixture\LoadCompetencyData',
            'Tests\CoreBundle\Fixture\LoadSessionData',
            'Tests\CoreBundle\Fixture\LoadSessionTypeData',
            'Tests\CoreBundle\Fixture\LoadCourseData',
            'Tests\CoreBundle\Fixture\LoadAamcPcrsData',
            'Tests\CoreBundle\Fixture\LoadProgramYearData'
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
        $data['programYears'] = ['2'];

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
    public function testPutCompetencyRemoveParent()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.competency')
            ->getAll()[2];
        $this->assertEquals('1', $data['parent']);
        //remove the parent
        unset($data['parent']);

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
    public function testFilterByTerm()
    {
        $competencies = $this->container->get('ilioscore.dataloader.competency')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_competencies', ['filters[terms]' => [1, 2, 3]]),
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
                $competencies[2]
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
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $competencies[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $competencies[2]
            ),
            $data[1]
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
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $competencies[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $competencies[2]
            ),
            $data[1]
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
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $competencies[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $competencies[2]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterBySchool()
    {
        $competencies = $this->container->get('ilioscore.dataloader.competency')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_competencies', ['filters[schools]' => [1]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['competencies'];
        $this->assertEquals(3, count($data), var_export($data, true));
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
        $this->assertEquals(
            $this->mockSerialize(
                $competencies[2]
            ),
            $data[2]
        );
    }
}
