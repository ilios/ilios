<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * @deprecated
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
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadOfferingData',
            'Ilios\CoreBundle\Tests\Fixture\LoadIlmSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshDescriptorData',
            'Ilios\CoreBundle\Tests\Fixture\LoadObjectiveData',
        ]);
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'courses',
            'sessions',
            'programYears'
        ];
    }

    /**
     * @group controllers_b
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
     * @group controllers_b
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
     * @group controllers_b
     */
    public function testPostTopic()
    {
        $data = $this->container->get('ilioscore.dataloader.topic')
            ->create();
        unset($data['courses']);
        unset($data['sessions']);
        unset($data['programYears']);
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
        unset($postData['courses']);
        unset($postData['sessions']);
        unset($postData['programYears']);

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

    /**
     * @group controllers
     */
    public function testFilterByCourse()
    {
        $topics = $this->container->get('ilioscore.dataloader.topic')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_topics', ['filters[courses][]' => 1]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['topics'];
        $this->assertEquals(2, count($data));
        $this->assertEquals(
            $this->mockSerialize(
                $topics[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $topics[1]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterBySession()
    {
        $topics = $this->container->get('ilioscore.dataloader.topic')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_topics', ['filters[sessions]' => [1, 2]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['topics'];
        $this->assertEquals(2, count($data));
        $this->assertEquals(
            $this->mockSerialize(
                $topics[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $topics[1]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterBySessionType()
    {
        $topics = $this->container->get('ilioscore.dataloader.topic')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_topics', ['filters[sessionTypes]' => [1, 2]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['topics'];
        $this->assertEquals(3, count($data));
        $this->assertEquals(
            $this->mockSerialize(
                $topics[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $topics[1]
            ),
            $data[1]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $topics[2]
            ),
            $data[2]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByInstructor()
    {
        $topics = $this->container->get('ilioscore.dataloader.topic')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_topics', ['filters[instructors][]' => 2]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['topics'];
        $this->assertEquals(2, count($data));
        $this->assertEquals(
            $this->mockSerialize(
                $topics[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $topics[1]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByInstructorGroup()
    {
        $topics = $this->container->get('ilioscore.dataloader.topic')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_topics', ['filters[instructorGroups]' => [1, 2, 3]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['topics'];
        $this->assertEquals(2, count($data));
        $this->assertEquals(
            $this->mockSerialize(
                $topics[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $topics[1]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByLearningMaterial()
    {
        $topics = $this->container->get('ilioscore.dataloader.topic')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_topics', ['filters[learningMaterials]' => [1, 2, 3]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['topics'];
        $this->assertEquals(2, count($data));
        $this->assertEquals(
            $this->mockSerialize(
                $topics[1]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $topics[2]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByCompetency()
    {
        $topics = $this->container->get('ilioscore.dataloader.topic')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_topics', ['filters[competencies]' => [1, 2]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['topics'];
        $this->assertEquals(3, count($data));
        $this->assertEquals(
            $this->mockSerialize(
                $topics[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $topics[1]
            ),
            $data[1]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $topics[2]
            ),
            $data[2]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByMeshDescriptor()
    {
        $topics = $this->container->get('ilioscore.dataloader.topic')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_topics', ['filters[meshDescriptors]' => ['abc1', 'abc2', 'abc3']]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['topics'];
        $this->assertEquals(3, count($data));
        $this->assertEquals(
            $this->mockSerialize(
                $topics[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $topics[1]
            ),
            $data[1]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $topics[2]
            ),
            $data[2]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByProgram()
    {
        $topics = $this->container->get('ilioscore.dataloader.topic')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_topics', ['filters[programs][]' => 1]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['topics'];
        $this->assertEquals(1, count($data));
        $this->assertEquals(
            $this->mockSerialize(
                $topics[0]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterBySchool()
    {
        $topics = $this->container->get('ilioscore.dataloader.topic')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_topics', ['filters[schools][]' => 1]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['topics'];
        $this->assertEquals(3, count($data));
        $this->assertEquals(
            $this->mockSerialize(
                $topics[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $topics[1]
            ),
            $data[1]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $topics[2]
            ),
            $data[2]
        );
    }
}
