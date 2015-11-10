<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * Topic controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class TopicControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadTopicData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSchoolData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseData',
            'Ilios\CoreBundle\Tests\Fixture\LoadProgramYearData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData'
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
     * @group controllers
     */
    public function testGetTopic()
    {
        $topic = $this->container
            ->get('ilioscore.dataloader.topic')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_topics',
                ['id' => $topic['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($topic),
            json_decode($response->getContent(), true)['topics'][0]
        );
    }

    /**
     * @group controllers
     */
    public function testGetAllTopics()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_topics'), null, $this->getAuthenticatedUserToken());
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.topic')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['topics']
        );
    }

    /**
     * @group controllers
     */
    public function testPostTopic()
    {
        $data = $this->container->get('ilioscore.dataloader.topic')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_topics'),
            json_encode(['topic' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['topics'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers
     */
    public function testPostTopicCourse()
    {
        $data = $this->container->get('ilioscore.dataloader.topic')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_topics'),
            json_encode(['topic' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['topics'][0]['id'];
        foreach ($postData['courses'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_courses',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['courses'][0];
            $this->assertTrue(in_array($newId, $data['topics']));
        }
    }

    /**
     * @group controllers
     */
    public function testPostTopicProgramYear()
    {
        $data = $this->container->get('ilioscore.dataloader.topic')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_topics'),
            json_encode(['topic' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['topics'][0]['id'];
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
            $this->assertTrue(in_array($newId, $data['topics']));
        }
    }

    /**
     * @group controllers
     */
    public function testPostTopicSession()
    {
        $data = $this->container->get('ilioscore.dataloader.topic')->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_topics'),
            json_encode(['topic' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $newId = json_decode($this->client->getResponse()->getContent(), true)['topics'][0]['id'];
        foreach ($postData['sessions'] as $id) {
            $this->createJsonRequest(
                'GET',
                $this->getUrl(
                    'get_sessions',
                    ['id' => $id]
                ),
                null,
                $this->getAuthenticatedUserToken()
            );
            $data = json_decode($this->client->getResponse()->getContent(), true)['sessions'][0];
            $this->assertTrue(in_array($newId, $data['topics']));
        }
    }

    /**
     * @group controllers
     */
    public function testPostBadTopic()
    {
        $invalidTopic = $this->container
            ->get('ilioscore.dataloader.topic')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_topics'),
            json_encode(['topic' => $invalidTopic]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testPutTopic()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.topic')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_topics',
                ['id' => $data['id']]
            ),
            json_encode(['topic' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['topic']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteTopic()
    {
        $topic = $this->container
            ->get('ilioscore.dataloader.topic')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_topics',
                ['id' => $topic['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_topics',
                ['id' => $topic['id']]
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
    public function testTopicNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_topics', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
