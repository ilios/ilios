<?php

namespace Tests\IliosApiBundle\Endpoints;

use Symfony\Component\HttpFoundation\Response;
use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * UsermaterialsTest API endpoint Test.
 * @group api_3
 */
class UsermaterialsTest extends AbstractEndpointTest
{
    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadOfferingData',
            'Tests\CoreBundle\Fixture\LoadIlmSessionData',
            'Tests\CoreBundle\Fixture\LoadUserData',
            'Tests\CoreBundle\Fixture\LoadSessionLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadCourseLearningMaterialData'
        ];
    }

    public function testGetAllMaterials()
    {
        $userId = 5;
        $materials = $this->getMaterials($userId);
        $this->assertCount(9, $materials, 'All expected materials returned');

        $this->assertEquals(16, count($materials[0]));
        $this->assertEquals('1', $materials[0]['id']);
        $this->assertEquals('1', $materials[0]['sessionLearningMaterial']);
        $this->assertEquals('1', $materials[0]['session']);
        $this->assertTrue($materials[0]['required']);
        $this->assertEquals('1', $materials[0]['position']);
        $this->assertRegExp('/^firstlm/', $materials[0]['title']);
        $this->assertRegExp('/^desc1/', $materials[0]['description']);
        $this->assertRegExp('/^author1/', $materials[0]['originalAuthor']);
        $this->assertRegExp('/^citation1/', $materials[0]['citation']);
        $this->assertEquals('citation', $materials[0]['mimetype']);
        $this->assertRegExp('/^session1Title/', $materials[0]['sessionTitle']);
        $this->assertEquals('1', $materials[0]['course']);
        $this->assertRegExp('/^firstCourse/', $materials[0]['courseTitle']);
        $this->assertEquals('2016-09-08T15:00:00+00:00', $materials[0]['firstOfferingDate']);
        $this->assertEquals(['first first'], $materials[0]['instructors']);
        $this->assertFalse($materials[0]['isBlanked']);

        $this->assertEquals(15, count($materials[1]));
        $this->assertEquals('1', $materials[1]['id']);
        $this->assertEquals('1', $materials[1]['course']);
        $this->assertFalse(array_key_exists('session', $materials[1]));
        $this->assertFalse($materials[1]['isBlanked']);

        $this->assertEquals(17, count($materials[2]));
        $this->assertEquals('3', $materials[2]['id']);
        $this->assertEquals('1', $materials[2]['course']);
        $this->assertEquals('2016-09-04T00:00:00+00:00', $materials[2]['firstOfferingDate']);
        $this->assertFalse(array_key_exists('session', $materials[2]));
        $this->assertFalse($materials[2]['isBlanked']);

        $this->assertEquals(18, count($materials[3]));
        $this->assertNotEmpty($materials[3]['startDate']);
        $this->assertFalse($materials[3]['isBlanked']);

        $this->assertEquals(10, count($materials[4]));
        $this->assertEquals('6', $materials[4]['id']);
        $this->assertEquals('6', $materials[4]['courseLearningMaterial']);
        $this->assertEquals('1', $materials[4]['course']);
        $this->assertEquals('4', $materials[4]['position']);
        $this->assertEquals('sixthlm', $materials[4]['title']);
        $this->assertEquals('firstCourse', $materials[4]['courseTitle']);
        $this->assertEquals('2016-09-04T00:00:00+00:00', $materials[4]['firstOfferingDate']);
        $this->assertEquals(0, count($materials[4]['instructors']));
        $this->assertNotEmpty($materials[4]['startDate']);
        $this->assertTrue($materials[4]['isBlanked']);

        $this->assertEquals(18, count($materials[5]));
        $this->assertNotEmpty($materials[5]['endDate']);
        $this->assertFalse($materials[5]['isBlanked']);

        $this->assertEquals(10, count($materials[6]));
        $this->assertEquals('8', $materials[6]['id']);
        $this->assertEquals('1', $materials[6]['course']);
        $this->assertEquals('eighthlm', $materials[6]['title']);
        $this->assertEquals('firstCourse', $materials[6]['courseTitle']);
        $this->assertEquals(0, count($materials[6]['instructors']));
        $this->assertNotEmpty($materials[6]['endDate']);
        $this->assertTrue($materials[6]['isBlanked']);

        $this->assertEquals(19, count($materials[7]));
        $this->assertNotEmpty($materials[7]['startDate']);
        $this->assertNotEmpty($materials[7]['endDate']);
        $this->assertFalse($materials[7]['isBlanked']);

        $this->assertEquals(11, count($materials[8]));
        $this->assertNotEmpty($materials[8]['startDate']);
        $this->assertNotEmpty($materials[8]['endDate']);
        $this->assertTrue($materials[8]['isBlanked']);
    }

    public function testGetAllMaterialsAsStudent()
    {
        $userId = 5;
        $materials = $this->getMaterials($userId, null, null, $userId);
        $this->assertCount(9, $materials, 'All expected materials returned');
        $materialIds = array_column($materials, 'id');
        $this->assertFalse(in_array('2', $materialIds, 'Draft material was filtered out.'));
    }

    public function testWhenViewingAnotherUsersEventsOnlyPublishedShows()
    {
        $userId = 5;
        $materials = $this->getMaterials($userId, null, null);
        $this->assertCount(9, $materials, 'All expected materials returned');
        $materialIds = array_column($materials, 'id');
        $this->assertFalse(in_array('2', $materialIds, 'Draft material was filtered out.'));
    }

    public function testGetMaterialsBeforeTheBeginningOfTime()
    {
        $userId = 5;
        $materials = $this->getMaterials($userId, $before = 0);

        $this->assertCount(0, $materials, 'No materials returned');
    }

    public function testGetMaterialsAfterTheBeginningOfTime()
    {
        $userId = 5;
        $materials = $this->getMaterials($userId, $before = null, $after = 0);
        $this->assertCount(9, $materials, 'All materials returned');
    }

    public function testGetMaterialsAfterTheEndOfTime()
    {
        $userId = 5;
        $materials = $this->getMaterials($userId, $before = null, $after = 2051233745);
        $this->assertCount(0, $materials, 'No materials returned');
    }

    public function testGetMaterialsBeforeTheEndOfTime()
    {
        $userId = 5;
        $materials = $this->getMaterials($userId, $before = 2051233745);
        $this->assertCount(9, $materials, 'All materials returned');
    }

    protected function getMaterials($userId, $before = null, $after = null, $authUser = null)
    {
        $parameters = [
            'version' => 'v1',
            'id' => $userId
        ];
        if (null !== $before) {
            $parameters['before'] = $before;
        }
        if (null !== $after) {
            $parameters['after'] = $after;
        }
        $url = $this->getUrl(
            'ilios_api_usermaterials',
            $parameters
        );

        $token = isset($authUser) ? $this->getTokenForUser($authUser) : $this->getAuthenticatedUserToken();
        $this->createJsonRequest(
            'GET',
            $url,
            null,
            $token
        );

        $response = $this->client->getResponse();

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            $this->fail("Unable to load url: {$url}");
        }

        $this->assertJsonResponse($response, Response::HTTP_OK);
        return json_decode($response->getContent(), true)['userMaterials'];
    }
}
