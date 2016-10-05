<?php

namespace Tests\CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;
use DateTime;
use Ilios\CoreBundle\Entity\AlertChangeTypeInterface;
use Ilios\CoreBundle\Entity\OfferingInterface;

/**
 * Offering controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class OfferingControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Tests\CoreBundle\Fixture\LoadOfferingData',
            'Tests\CoreBundle\Fixture\LoadLearnerGroupData',
            'Tests\CoreBundle\Fixture\LoadInstructorGroupData',
            'Tests\CoreBundle\Fixture\LoadIlmSessionData',
            'Tests\CoreBundle\Fixture\LoadAlertChangeTypeData'
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
    public function testGetOffering()
    {
        $offering = $this->container
            ->get('ilioscore.dataloader.offering')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_offerings',
                ['id' => $offering['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['offerings'][0];
        $updatedAt = new DateTime($data['updatedAt']);
        unset($data['updatedAt']);
        $this->assertEquals(
            $this->mockSerialize($offering),
            $data
        );
        $now = new DateTime();
        $diff = $now->diff($updatedAt);
        $this->assertTrue($diff->i < 10, 'The updatedAt timestamp is within the last 10 minutes');
    }

    /**
     * @group controllers_b
     */
    public function testGetAllOfferings()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_offerings'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = [];
        $responses = json_decode($response->getContent(), true)['offerings'];
        $now = new DateTime();
        foreach ($responses as $response) {
            $updatedAt = new DateTime($response['updatedAt']);
            unset($response['updatedAt']);
            $diff = $now->diff($updatedAt);
            $this->assertTrue($diff->i < 10, 'The updatedAt timestamp is within the last 10 minutes');
            $data[] = $response;
        }
        $allOfferings = $this->container
            ->get('ilioscore.dataloader.offering')
            ->getAll();

        // since there is no guarantee that all fixture offerings are returned from the server
        // (think 'soft deletes' on owning sessions/courses/schools),
        // we need to filter out any offerings from the original data set
        // that did not get returned before we can do a comparison.
        // @todo replace this with a better filtering mechanism based on deletion of parent entities. [ST 2015/11/02]
        $filteredOfferings = array_filter($allOfferings, function (array $offering) use ($data) {
            $offeringId = $offering['id'];
            $found = array_reduce($data, function ($found, array $offering) use ($offeringId) {
                return $found || $offering['id'] === $offeringId;
            }, false);
            return $found;
        });

        //Some of the offering time stamps are dynamic so we can't really test them
        $removeStartAndEndDate = function (array $arr) {
            unset($arr['startDate']);
            unset($arr['endDate']);
            return $arr;
        };
        $mappedData = array_map($removeStartAndEndDate, $data);
        $mappedOfferings = array_map($removeStartAndEndDate, $filteredOfferings);

        $this->assertEquals($this->mockSerialize(array_values($mappedOfferings)), $mappedData);
    }

    /**
     * @group controllers_b
     */
    public function testPostOffering()
    {
        $data = $this->container->get('ilioscore.dataloader.offering')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_offerings'),
            json_encode(['offering' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $responseData = json_decode($response->getContent(), true)['offerings'][0];
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

        // check if that offering creation resulted in an alert creation
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'cget_alerts',
                [
                    'filters[tableRowId]' => $responseData['id'],
                    'filters[tableName]' => 'offering',
                    'filters[dispatched]' => '0'
                ]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $responseData = json_decode($response->getContent(), true)['alerts'][0];
        $this->assertEquals(count($responseData['changeTypes']), 1);
        $this->assertEquals($responseData['changeTypes'][0], AlertChangeTypeInterface::CHANGE_TYPE_NEW_OFFERING);
        // Here, we could dive further in
        // and verify that the user who made this request gets returned
        // as instigator, and that the returned recipient is indeed
        // the school that owns the parent course of this offering.
        // For now, let's just verify that a single instigator and recipient is returned.
        // [ST 2015/10/08]
        $this->assertEquals(count($responseData['instigators'][0]), 1);
        $this->assertEquals(count($responseData['recipients'][0]), 1);
    }

    /**
     * @group controllers_b
     */
    public function testPostBadOffering()
    {
        $invalidOffering = $this->container
            ->get('ilioscore.dataloader.offering')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_offerings'),
            json_encode(['offering' => $invalidOffering]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers_b
     */
    public function testPutOffering()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.offering')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_offerings',
                ['id' => $data['id']]
            ),
            json_encode(['offering' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $responseData = json_decode($response->getContent(), true)['offering'];
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

        // check if that offering creation resulted in an alert creation
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'cget_alerts',
                [
                    'filters[tableRowId]' => $data['id'],
                    'filters[tableName]' => 'offering',
                    'filters[dispatched]' => '0'
                ]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);

        $responseData = json_decode($response->getContent(), true)['alerts'];
        $this->assertEmpty($responseData);

        $postData['room'] .= strrev($postData['room']);
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_offerings',
                ['id' => $data['id']]
            ),
            json_encode(['offering' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        // check if that offering creation resulted in an alert creation
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'cget_alerts',
                [
                    'filters[tableRowId]' => $data['id'],
                    'filters[tableName]' => 'offering',
                    'filters[dispatched]' => '0'
                ]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $responseData = json_decode($response->getContent(), true)['alerts'];
        $this->assertNotEmpty($responseData);
        $this->assertEquals(count($responseData), 1);
        $alert = $responseData[0];
        $this->assertEquals(count($alert['changeTypes']), 1);
        $this->assertEquals($alert['changeTypes'][0], AlertChangeTypeInterface::CHANGE_TYPE_LOCATION);

        // send another update
        // this time, change the start/end time and learner/instructor(group) associations
        $startDate = new \DateTime($data['startDate'], new \DateTimeZone('UTC'));
        $endDate = new \DateTime($data['endDate'], new \DateTimeZone('UTC'));
        $postData['startDate'] = $startDate->add(new \DateInterval('P10D'))->format('c');
        $postData['endDate'] = $endDate->add(new \DateInterval('P10D'))->format('c');
        $postData['instructors'] = [];
        $postData['instructorGroups'] = [];
        $postData['learners'] = [];
        $postData['learnerGroups'] = [];
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_offerings',
                ['id' => $data['id']]
            ),
            json_encode(['offering' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'cget_alerts',
                [
                    'filters[tableRowId]' => $data['id'],
                    'filters[tableName]' => 'offering',
                    'filters[dispatched]' => '0'
                ]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $responseData = json_decode($response->getContent(), true)['alerts'];
        $this->assertNotEmpty($responseData);
        $this->assertEquals(count($responseData), 1);
        $alert = $responseData[0];
        $this->assertNotEmpty($alert['changeTypes']);
        $this->assertTrue(in_array(AlertChangeTypeInterface::CHANGE_TYPE_LOCATION, $alert['changeTypes']));
        $this->assertTrue(in_array(AlertChangeTypeInterface::CHANGE_TYPE_TIME, $alert['changeTypes']));
        $this->assertTrue(in_array(AlertChangeTypeInterface::CHANGE_TYPE_INSTRUCTOR, $alert['changeTypes']));
        $this->assertTrue(in_array(AlertChangeTypeInterface::CHANGE_TYPE_LEARNER_GROUP, $alert['changeTypes']));
    }

    /**
     * @group controllers
     */
    public function testDeleteOffering()
    {
        $offering = $this->container
            ->get('ilioscore.dataloader.offering')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_offerings',
                ['id' => $offering['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_offerings',
                ['id' => $offering['id']]
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
    public function testOfferingNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_offerings', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
    
    
    /**
     * Grab the first offering from the fixtures and get the updatedAt time
     * from the server.
     *
     * @return DateTime
     */
    protected function getOfferingUpdatedAt()
    {
        $offering = $this->container
            ->get('ilioscore.dataloader.offering')
            ->getOne();

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_offerings',
                ['id' => $offering['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $data = json_decode($response->getContent(), true)['offerings'][0];
        return  new DateTime($data['updatedAt']);
    }

    /**
     * Test to see that the updatedAt timestamp has increased by at least one second
     * @param  DateTime $original
     */
    protected function checkUpdatedAtIncreased(DateTime $original)
    {
        $now = $this->getOfferingUpdatedAt();
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
    public function testUpdatingLearnerGroupUpdatesOfferingStamp()
    {
        $firstUpdatedAt = $this->getOfferingUpdatedAt();
        sleep(2); //wait for two seconds
        
        $lg = $this->container
            ->get('ilioscore.dataloader.learnergroup')
            ->getOne();
        
        $postData = $lg;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['children']);

        $postData['title'] = $lg['title'] . 'some more text';
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_learnergroups',
                ['id' => $lg['id']]
            ),
            json_encode(['learnerGroup' => $postData]),
            $this->getAuthenticatedUserToken()
        );
        $this->assertJsonResponse($this->client->getResponse(), Codes::HTTP_OK);
        $this->checkUpdatedAtIncreased($firstUpdatedAt);
    }

    /**
     * @group controllers
     */
    public function testUpdatingInstructorGroupUpdatesOfferingStamp()
    {
        $firstUpdatedAt = $this->getOfferingUpdatedAt();
        sleep(2); //wait for two seconds
        
        $ig = $this->container
            ->get('ilioscore.dataloader.instructorgroup')
            ->getOne();
        
        $postData = $ig;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        $postData['title'] = $ig['title'] . 'some more text';
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_instructorgroups',
                ['id' => $ig['id']]
            ),
            json_encode(['instructorGroup' => $postData]),
            $this->getAuthenticatedUserToken()
        );
        $this->assertJsonResponse($this->client->getResponse(), Codes::HTTP_OK);
        $this->checkUpdatedAtIncreased($firstUpdatedAt);
    }

    /**
     * @group controllers
     */
    public function testUpdatingInstructorUpdatesOfferingStamp()
    {
        $offering = $this->container
            ->get('ilioscore.dataloader.offering')
            ->getOne();

        $postData = $offering;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $firstUpdatedAt = $this->getOfferingUpdatedAt();
        sleep(2); //wait for two seconds

        $postData['instructors'] = ['1'];

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_offerings',
                ['id' => $offering['id']]
            ),
            json_encode(['offering' => $postData]),
            $this->getAuthenticatedUserToken()
        );
        $this->assertJsonResponse($this->client->getResponse(), Codes::HTTP_OK);
        $this->checkUpdatedAtIncreased($firstUpdatedAt);
    }

    /**
     * @group controllers
     */
    public function testUpdatingILearnerUpdatesOfferingStamp()
    {
        $offering = $this->container
            ->get('ilioscore.dataloader.offering')
            ->getOne();

        $postData = $offering;
        //unset any parameters which should not be POSTed
        unset($postData['id']);

        $firstUpdatedAt = $this->getOfferingUpdatedAt();
        sleep(2); //wait for two seconds

        $postData['learners'] = ['1'];

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_offerings',
                ['id' => $offering['id']]
            ),
            json_encode(['offering' => $postData]),
            $this->getAuthenticatedUserToken()
        );
        $this->assertJsonResponse($this->client->getResponse(), Codes::HTTP_OK);
        $this->checkUpdatedAtIncreased($firstUpdatedAt);
    }
}
