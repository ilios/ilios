<?php

namespace Tests\CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * LearnerGroup controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class LearnerGroupControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Tests\CoreBundle\Fixture\LoadLearnerGroupData',
            'Tests\CoreBundle\Fixture\LoadCohortData',
            'Tests\CoreBundle\Fixture\LoadIlmSessionData',
            'Tests\CoreBundle\Fixture\LoadOfferingData',
            'Tests\CoreBundle\Fixture\LoadInstructorGroupData',
            'Tests\CoreBundle\Fixture\LoadUserData',
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
    public function testGetLearnerGroup()
    {
        $learnerGroup = $this->container
            ->get('ilioscore.dataloader.learnergroup')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_learnergroups',
                ['id' => $learnerGroup['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($learnerGroup),
            json_decode($response->getContent(), true)['learnerGroups'][0]
        );
    }

    /**
     * @group controllers_a
     */
    public function testGetAllLearnerGroups()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_learnergroups'), null, $this->getAuthenticatedUserToken());
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.learnergroup')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['learnerGroups']
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostLearnerGroup()
    {
        $data = $this->container->get('ilioscore.dataloader.learnergroup')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['children']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learnergroups'),
            json_encode(['learnerGroup' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['learnerGroups'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostLearnerGroupIlmSession()
    {
        $data = $this->container->get('ilioscore.dataloader.learnergroup')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['children']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learnergroups'),
            json_encode(['learnerGroup' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['learnerGroups'][0]['id'];
        foreach ($postData['ilmSessions'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_ilmsessions',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['ilmSessions'][0];
            $this->assertTrue(in_array($newId, $data['learnerGroups']));
        }
    }

    /**
     * @group controllers_a
     */
    public function testPostLearnerGroupOffering()
    {
        $data = $this->container->get('ilioscore.dataloader.learnergroup')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['children']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learnergroups'),
            json_encode(['learnerGroup' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['learnerGroups'][0]['id'];
        foreach ($postData['offerings'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_offerings',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['offerings'][0];
            $this->assertTrue(in_array($newId, $data['learnerGroups']));
        }
    }

    /**
     * @group controllers
     */
    public function testPostBadLearnerGroup()
    {
        $invalidLearnerGroup = $this->container
            ->get('ilioscore.dataloader.learnergroup')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_learnergroups'),
            json_encode(['learnerGroup' => $invalidLearnerGroup]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testPutLearnerGroup()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.learnergroup')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['children']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_learnergroups',
                ['id' => $data['id']]
            ),
            json_encode(['learnerGroup' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['learnerGroup']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteLearnerGroup()
    {
        $learnerGroup = $this->container
            ->get('ilioscore.dataloader.learnergroup')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_learnergroups',
                ['id' => $learnerGroup['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_learnergroups',
                ['id' => $learnerGroup['id']]
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
    public function testLearnerGroupNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_learnergroups', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }

    /**
     * @group controllers
     */
    public function testFilterByCohorts()
    {
        $learnerGroups = $this->container->get('ilioscore.dataloader.learnergroup')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_learnergroups', ['filters[cohorts]' => [2]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['learnerGroups'];
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $learnerGroups[1]
            ),
            $data[0]
        );

        //Test the singular filter as well #1409
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_learnergroups', ['filters[cohort]' => 2]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['learnerGroups'];
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $learnerGroups[1]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByParent()
    {
        $learnerGroups = $this->container->get('ilioscore.dataloader.learnergroup')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_learnergroups', ['filters[parents]' => [1]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['learnerGroups'];
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $learnerGroups[3]
            ),
            $data[0]
        );

        //Test the singular filter as well #1409
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_learnergroups', ['filters[parent]' => 1]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['learnerGroups'];
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $learnerGroups[3]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByNoParent()
    {
        $learnerGroups = $this->container->get('ilioscore.dataloader.learnergroup')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_learnergroups', ['filters[parents]' => 'null']),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['learnerGroups'];
        $this->assertEquals(3, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $learnerGroups[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $learnerGroups[1]
            ),
            $data[1]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $learnerGroups[2]
            ),
            $data[2]
        );

        //Test the singular filter as well #1409
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_learnergroups', ['filters[parent]' => 'null']),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['learnerGroups'];
        $this->assertEquals(3, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $learnerGroups[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $learnerGroups[1]
            ),
            $data[1]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $learnerGroups[2]
            ),
            $data[2]
        );
    }
}
