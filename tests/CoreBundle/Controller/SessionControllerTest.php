<?php

namespace Tests\CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;
use DateTime;

/**
 * Session controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class SessionControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Tests\CoreBundle\Fixture\LoadSessionData',
            'Tests\CoreBundle\Fixture\LoadTermData',
            'Tests\CoreBundle\Fixture\LoadSessionDescriptionData',
            'Tests\CoreBundle\Fixture\LoadSessionLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadOfferingData',
            'Tests\CoreBundle\Fixture\LoadSessionLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadCourseLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadLearningMaterialStatusData',
            'Tests\CoreBundle\Fixture\LoadIlmSessionData',
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
    public function testGetSession()
    {
        $session = $this->container
            ->get('ilioscore.dataloader.session')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_sessions',
                ['id' => $session['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['sessions'][0];
        $updatedAt = new DateTime($data['updatedAt']);
        unset($data['updatedAt']);
        $this->assertEquals(
            $this->mockSerialize($session),
            $data
        );
        $now = new DateTime();
        $diff = $now->diff($updatedAt);
        $this->assertTrue($diff->y < 1, 'The updatedAt timestamp is within the last year');
    }

    /**
     * @group controllers_b
     */
    public function testGetAllSessions()
    {
        $sessions = $this->container->get('ilioscore.dataloader.session')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_sessions'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = [];
        $responses = json_decode($response->getContent(), true)['sessions'];
        $now = new DateTime();
        foreach ($responses as $response) {
            $updatedAt = new DateTime($response['updatedAt']);
            unset($response['updatedAt']);
            $diff = $now->diff($updatedAt);
            $this->assertTrue($diff->y < 1, 'The updatedAt timestamp is within the last year');
            $data[] = $response;
        }
        $this->assertEquals(
            array_values($this->mockSerialize(
                $sessions
            )),
            array_values($data)
        );
    }

    /**
     * @group controllers_b
     */
    public function testPostSession()
    {
        $data = $this->container->get('ilioscore.dataloader.session')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['offerings']);
        unset($postData['learningMaterials']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_sessions'),
            json_encode(['session' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $responseData = json_decode($response->getContent(), true)['sessions'][0];
        $updatedAt = new DateTime($responseData['updatedAt']);
        unset($responseData['updatedAt']);
        $this->assertEquals(
            $data,
            $responseData,
            $response->getContent()
        );
        $now = new DateTime();
        $diff = $now->diff($updatedAt);
        $this->assertTrue($diff->i < 10, 'The updatedAt timestamp is within the last 10 minutes');
    }

    /**
     * @group controllers_b
     */
    public function testPostBadSession()
    {
        $invalidSession = $this->container
            ->get('ilioscore.dataloader.session')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_sessions'),
            json_encode(['session' => $invalidSession]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers_b
     */
    public function testPutSession()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.session')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['offerings']);
        unset($postData['learningMaterials']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_sessions',
                ['id' => $data['id']]
            ),
            json_encode(['session' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $responseData = json_decode($response->getContent(), true)['session'];
        $updatedAt = new DateTime($responseData['updatedAt']);
        unset($responseData['updatedAt']);
        $this->assertEquals(
            $data,
            $responseData,
            $response->getContent()
        );
        $now = new DateTime();
        $diff = $now->diff($updatedAt);
        $this->assertTrue($diff->i < 10, 'The updatedAt timestamp is within the last 10 minutes');
    }

    /**
     * @group controllers
     */
    public function testDeleteSession()
    {
        $session = $this->container
            ->get('ilioscore.dataloader.session')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_sessions',
                ['id' => $session['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_sessions',
                ['id' => $session['id']]
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
    public function testSessionNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_sessions', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
    
    /**
     * Grab the first session from the fixtures and get the updatedAt time
     * from the server.
     *
     * @param int $sessionId
     * @return DateTime
     * @throws \Exception
     */
    protected function getSessionUpdatedAt($sessionId)
    {
        $sessions = $this->container
            ->get('ilioscore.dataloader.session')
            ->getAll();
        $matchedSessions = array_filter($sessions, function ($arr) use ($sessionId) {
            return $arr['id'] === $sessionId;
        });
        if (!count($matchedSessions)) {
            throw new \Exception("Unable to find session: {$sessionId} in ", var_export($sessions, true));
        }
        $session = array_values($matchedSessions)[0];
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_sessions',
                ['id' => $session['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $data = json_decode($response->getContent(), true)['sessions'][0];
        return new DateTime($data['updatedAt']);
    }
    
    /**
     * Test to see that the updatedAt timestamp has increased by at least one second
     * @param integer $sessionId
     * @param  DateTime $original
     */
    protected function checkUpdatedAtIncreased($sessionId, DateTime $original)
    {
        $now = $this->getSessionUpdatedAt($sessionId);
        $diff = $now->getTimestamp() - $original->getTimestamp();
        $this->assertTrue(
            $diff > 1,
            'The updatedAt timestamp has increased.  Original: ' . $original->format('c') .
            ' Now: ' . $now->format('c')
        );
    }

    /**
     * @group controllers
     */
    public function testUpdatingIlmUpdatesSessionStamp()
    {
        $ilm = $this->container
            ->get('ilioscore.dataloader.ilmsession')
            ->getOne();
            
        $firstUpdatedAt = $this->getSessionUpdatedAt($ilm['session']);
        //wait for two seconds so there is some difference
        //between the first stamp and the update
        sleep(2);
        
        $postData = $ilm;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        $postData['hours'] = $ilm['hours'] + 1;
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_ilmsessions',
                ['id' => $ilm['id']]
            ),
            json_encode(['ilmSession' => $postData]),
            $this->getAuthenticatedUserToken()
        );
        $this->assertJsonResponse($this->client->getResponse(), Codes::HTTP_OK);
        $this->checkUpdatedAtIncreased($ilm['session'], $firstUpdatedAt);
    }

    /**
     * @group controllers
     */
    public function testUpdatingIlmInstructorUpdatesSessionStamp()
    {
        $ilm = $this->container
            ->get('ilioscore.dataloader.ilmsession')
            ->getOne();
            
        $firstUpdatedAt = $this->getSessionUpdatedAt($ilm['session']);
        //wait for two seconds so there is some difference
        //between the first stamp and the update
        sleep(2);
        
        $postData = $ilm;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        $postData['instructors'] = ["1", "2"];
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_ilmsessions',
                ['id' => $ilm['id']]
            ),
            json_encode(['ilmSession' => $postData]),
            $this->getAuthenticatedUserToken()
        );
        $this->assertJsonResponse($this->client->getResponse(), Codes::HTTP_OK);
        $this->checkUpdatedAtIncreased($ilm['session'], $firstUpdatedAt);
    }

    /**
     * @group controllers
     */
    public function testUpdatingIlmInstructorGroupsUpdatesSessionStamp()
    {
        $ilm = $this->container
            ->get('ilioscore.dataloader.ilmsession')
            ->getOne();
            
        $firstUpdatedAt = $this->getSessionUpdatedAt($ilm['session']);
        //wait for two seconds so there is some difference
        //between the first stamp and the update
        sleep(2);
        
        $postData = $ilm;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        $postData['instructorGroups'] = ["1", "2"];
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_ilmsessions',
                ['id' => $ilm['id']]
            ),
            json_encode(['ilmSession' => $postData]),
            $this->getAuthenticatedUserToken()
        );
        $this->assertJsonResponse($this->client->getResponse(), Codes::HTTP_OK);
        $this->checkUpdatedAtIncreased($ilm['session'], $firstUpdatedAt);
    }

    /**
     * @group controllers
     */
    public function testUpdatingIlmLearnerGroupsUpdatesSessionStamp()
    {
        $ilm = $this->container
            ->get('ilioscore.dataloader.ilmsession')
            ->getOne();
            
        $firstUpdatedAt = $this->getSessionUpdatedAt($ilm['session']);
        //wait for two seconds so there is some difference
        //between the first stamp and the update
        sleep(2);
        
        $postData = $ilm;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        $postData['learnerGroups'] = ["1", "2"];
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_ilmsessions',
                ['id' => $ilm['id']]
            ),
            json_encode(['ilmSession' => $postData]),
            $this->getAuthenticatedUserToken()
        );
        $this->assertJsonResponse($this->client->getResponse(), Codes::HTTP_OK);
        $this->checkUpdatedAtIncreased($ilm['session'], $firstUpdatedAt);
    }

    /**
     * @group controllers
     */
    public function testUpdatingIlmLearnersUpdatesSessionStamp()
    {
        $ilm = $this->container
            ->get('ilioscore.dataloader.ilmsession')
            ->getOne();
            
        $firstUpdatedAt = $this->getSessionUpdatedAt($ilm['session']);
        //wait for two seconds so there is some difference
        //between the first stamp and the update
        sleep(2);
        
        $postData = $ilm;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        $postData['learners'] = ["1", "2"];
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_ilmsessions',
                ['id' => $ilm['id']]
            ),
            json_encode(['ilmSession' => $postData]),
            $this->getAuthenticatedUserToken()
        );
        $this->assertJsonResponse($this->client->getResponse(), Codes::HTTP_OK);
        $this->checkUpdatedAtIncreased($ilm['session'], $firstUpdatedAt);
    }

    /**
     * @group controllers
     */
    public function testUpdatingLearningMaterialUpdatesSessionStamp()
    {
        $firstUpdatedAt = $this->getSessionUpdatedAt(1);
        sleep(2); //wait for two seconds
        
        $lm = $this->container
            ->get('ilioscore.dataloader.learningmaterial')
            ->getOne();
        
        $postData = $lm;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['courseLearningMaterials']);
        unset($postData['sessionLearningMaterials']);

        $postData['status'] = '2';
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_learningmaterials',
                ['id' => $lm['id']]
            ),
            json_encode(['learningMaterial' => $postData]),
            $this->getAuthenticatedUserToken()
        );
        $this->assertJsonResponse($this->client->getResponse(), Codes::HTTP_OK);
        $this->checkUpdatedAtIncreased(1, $firstUpdatedAt);
    }

    /**
     * @group controllers
     */
    public function testNewSessionLearningMaterialUpdatesSessionStamp()
    {
        $firstUpdatedAt = $this->getSessionUpdatedAt(1);
        sleep(2); //wait for two seconds
        
        $lm = $this->container
            ->get('ilioscore.dataloader.sessionlearningmaterial')
            ->create();
        
        $postData = $lm;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        $postData['session'] = '1';
        $this->createJsonRequest(
            'POST',
            $this->getUrl(
                'post_sessionlearningmaterials'
            ),
            json_encode(['sessionLearningMaterial' => $postData]),
            $this->getAuthenticatedUserToken()
        );
        $this->assertJsonResponse($this->client->getResponse(), Codes::HTTP_CREATED);
        $this->checkUpdatedAtIncreased(1, $firstUpdatedAt);
    }

    /**
     * @group controllers
     */
    public function testUpdatingSessionLearningMaterialUpdatesSessionStamp()
    {
        $firstUpdatedAt = $this->getSessionUpdatedAt(1);
        sleep(2); //wait for two seconds
        
        $lm = $this->container
            ->get('ilioscore.dataloader.sessionlearningmaterial')
            ->getOne();
        
        $postData = $lm;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        $postData['required'] = true;
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_sessionlearningmaterials',
                ['id' => $lm['id']]
            ),
            json_encode(['sessionLearningMaterial' => $postData]),
            $this->getAuthenticatedUserToken()
        );
        $this->assertJsonResponse($this->client->getResponse(), Codes::HTTP_OK);
        $this->checkUpdatedAtIncreased(1, $firstUpdatedAt);
    }

    /**
     * @group controllers
     */
    public function testDeletingSessionLearningMaterialUpdatesSessionStamp()
    {
        $firstUpdatedAt = $this->getSessionUpdatedAt(1);
        sleep(2); //wait for two seconds
        
        $session = $this->container
            ->get('ilioscore.dataloader.session')
            ->getOne();
        
        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_sessionlearningmaterials',
                ['id' => $session['learningMaterials'][0]]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
        $this->checkUpdatedAtIncreased(1, $firstUpdatedAt);
    }

    /**
     * @group controllers
     */
    public function testDeletingSessionDescriptionUpdatesSessionStamp()
    {
        $firstUpdatedAt = $this->getSessionUpdatedAt(1);
        sleep(2); //wait for two seconds
        
        $session = $this->container
            ->get('ilioscore.dataloader.session')
            ->getOne();
        
        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_sessiondescriptions',
                ['id' => $session['sessionDescription']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
        $this->checkUpdatedAtIncreased(1, $firstUpdatedAt);
    }

    /**
     * @group controllers
     */
    public function testUpdatingSessionDescriptionUpdatesSessionStamp()
    {
        $firstUpdatedAt = $this->getSessionUpdatedAt(1);
        sleep(2); //wait for two seconds
        
        $descriptions = $this->container
            ->get('ilioscore.dataloader.sessionDescription')
            ->getAll();
        $descriptions = array_filter($descriptions, function ($arr) {
            return $arr['session'] === '1';
        });
        
        $description = array_values($descriptions)[0];
        $postData = $description;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        $postData['description'] = 'something new';
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_sessiondescriptions',
                ['id' => $description['id']]
            ),
            json_encode(['sessionDescription' => $postData]),
            $this->getAuthenticatedUserToken()
        );
        $this->assertJsonResponse($this->client->getResponse(), Codes::HTTP_OK);
        $this->checkUpdatedAtIncreased(1, $firstUpdatedAt);
    }

    /**
     * @group controllers
     */
    public function testFilterByAttireRequired()
    {
        $sessions = $this->container->get('ilioscore.dataloader.session')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_sessions', ['filters[attireRequired]' => true]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['updatedAt']);
            return $arr;
        }, json_decode($response->getContent(), true)['sessions']);

        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[1]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterBySupplemental()
    {
        $sessions = $this->container->get('ilioscore.dataloader.session')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_sessions', ['filters[supplemental]' => true]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['updatedAt']);
            return $arr;
        }, json_decode($response->getContent(), true)['sessions']);
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[1]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[2]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByIds()
    {
        $sessions = $this->container->get('ilioscore.dataloader.session')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_sessions', ['filters[id]' => [1,3]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['updatedAt']);
            return $arr;
        }, json_decode($response->getContent(), true)['sessions']);
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[2]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterBySupplementalAndAttireRequired()
    {
        $sessions = $this->container->get('ilioscore.dataloader.session')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_sessions', ['filters[supplemental]' => true, 'filters[attireRequired]' => true]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['updatedAt']);
            return $arr;
        }, json_decode($response->getContent(), true)['sessions']);
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[1]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterBySessionType()
    {
        $sessions = $this->container->get('ilioscore.dataloader.session')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_sessions', ['filters[sessionType]' => 2]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['updatedAt']);
            return $arr;
        }, json_decode($response->getContent(), true)['sessions']);
        $this->assertEquals(3, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[1]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[2]
            ),
            $data[1]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[3]
            ),
            $data[2]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterBySupplementalAndSessionType()
    {
        $sessions = $this->container->get('ilioscore.dataloader.session')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_sessions', ['filters[supplemental]' => true, 'filters[sessionType]' => 2]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['updatedAt']);
            return $arr;
        }, json_decode($response->getContent(), true)['sessions']);
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[1]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[2]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByTerm()
    {
        $sessions = $this->container->get('ilioscore.dataloader.session')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_sessions', ['filters[terms][]' => 1]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['updatedAt']);
            return $arr;
        }, json_decode($response->getContent(), true)['sessions']);
        $this->assertEquals(1, count($data));
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[1]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByCourse()
    {
        $sessions = $this->container->get('ilioscore.dataloader.session')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_sessions', ['filters[course]' => 4]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['updatedAt']);
            return $arr;
        }, json_decode($response->getContent(), true)['sessions']);
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[3]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByCourseMultiple()
    {
        $sessions = $this->container->get('ilioscore.dataloader.session')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_sessions', ['filters[course]' => [1, 4]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['updatedAt']);
            return $arr;
        }, json_decode($response->getContent(), true)['sessions']);
        $this->assertEquals(3, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[1]
            ),
            $data[1]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[3]
            ),
            $data[2]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByProgram()
    {
        $sessions = $this->container->get('ilioscore.dataloader.session')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_sessions', ['filters[programs][]' => 2]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['updatedAt']);
            return $arr;
        }, json_decode($response->getContent(), true)['sessions']);
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[3]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByInstructor()
    {
        $sessions = $this->container->get('ilioscore.dataloader.session')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_sessions', ['filters[instructors]' => [2]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['updatedAt']);
            return $arr;
        }, json_decode($response->getContent(), true)['sessions']);
        $this->assertEquals(5, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[1]
            ),
            $data[1]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[4]
            ),
            $data[2]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[5]
            ),
            $data[3]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[6]
            ),
            $data[4]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByInstructorGroup()
    {
        $sessions = $this->container->get('ilioscore.dataloader.session')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_sessions', ['filters[instructorGroups][]' => 1]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['updatedAt']);
            return $arr;
        }, json_decode($response->getContent(), true)['sessions']);
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[4]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByLearningMaterial()
    {
        $sessions = $this->container->get('ilioscore.dataloader.session')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_sessions', ['filters[learningMaterials]' => [3]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['updatedAt']);
            return $arr;
        }, json_decode($response->getContent(), true)['sessions']);
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[2]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByCompetency()
    {
        $sessions = $this->container->get('ilioscore.dataloader.session')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_sessions', ['filters[competencies][]' => 1]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['updatedAt']);
            return $arr;
        }, json_decode($response->getContent(), true)['sessions']);
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[0]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByMeshDescriptor()
    {
        $sessions = $this->container->get('ilioscore.dataloader.session')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_sessions', ['filters[meshDescriptors]' => ['abc2', 'abc3']]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['updatedAt']);
            return $arr;
        }, json_decode($response->getContent(), true)['sessions']);
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[2]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[3]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterBySchool()
    {
        $sessions = $this->container->get('ilioscore.dataloader.session')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_sessions', ['filters[schools][]' => 2]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = array_map(function ($arr) {
            unset($arr['updatedAt']);
            return $arr;
        }, json_decode($response->getContent(), true)['sessions']);
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $sessions[3]
            ),
            $data[0]
        );
    }
}
