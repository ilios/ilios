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
        $this->assertCount(10, $materials, 'All expected materials returned');

        $this->assertEquals(14, count($materials[0]));
        $this->assertEquals('1', $materials[0]['id']);
        $this->assertEquals('1', $materials[0]['session']);
        $this->assertTrue($materials[0]['required']);
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

        $this->assertEquals(13, count($materials[1]));
        $this->assertEquals('1', $materials[1]['id']);
        $this->assertEquals('1', $materials[1]['course']);
        $this->assertFalse(array_key_exists('session', $materials[1]));
        $this->assertFalse($materials[1]['isBlanked']);

        $this->assertEquals(13, count($materials[2]));
        $this->assertEquals('2', $materials[2]['id']);
        $this->assertEquals('1', $materials[2]['course']);
        $this->assertEquals('2016-09-04T00:00:00+00:00', $materials[2]['firstOfferingDate']);
        $this->assertFalse(array_key_exists('session', $materials[2]));
        $this->assertFalse($materials[2]['isBlanked']);

        $this->assertEquals(14, count($materials[3]));
        $this->assertEquals('3', $materials[3]['id']);
        $this->assertEquals('1', $materials[3]['course']);
        $this->assertEquals('2016-09-04T00:00:00+00:00', $materials[2]['firstOfferingDate']);
        $this->assertFalse(array_key_exists('session', $materials[3]));
        $this->assertFalse($materials[3]['isBlanked']);

        $this->assertEquals(15, count($materials[4]));
        $this->assertNotEmpty($materials[4]['startDate']);
        $this->assertFalse($materials[4]['isBlanked']);

        $this->assertEquals(7, count($materials[5]));
        $this->assertEquals('6', $materials[5]['id']);
        $this->assertEquals('1', $materials[5]['course']);
        $this->assertEquals('sixthlm', $materials[5]['title']);
        $this->assertEquals('firstCourse', $materials[5]['courseTitle']);
        $this->assertEquals(0, count($materials[5]['instructors']));
        $this->assertNotEmpty($materials[5]['startDate']);
        $this->assertTrue($materials[5]['isBlanked']);

        $this->assertEquals(15, count($materials[6]));
        $this->assertNotEmpty($materials[6]['endDate']);
        $this->assertFalse($materials[6]['isBlanked']);

        $this->assertEquals(7, count($materials[7]));
        $this->assertEquals('8', $materials[7]['id']);
        $this->assertEquals('1', $materials[7]['course']);
        $this->assertEquals('eighthlm', $materials[7]['title']);
        $this->assertEquals('firstCourse', $materials[7]['courseTitle']);
        $this->assertEquals(0, count($materials[7]['instructors']));
        $this->assertNotEmpty($materials[7]['endDate']);
        $this->assertTrue($materials[7]['isBlanked']);

        $this->assertEquals(16, count($materials[8]));
        $this->assertNotEmpty($materials[8]['startDate']);
        $this->assertNotEmpty($materials[8]['endDate']);
        $this->assertFalse($materials[8]['isBlanked']);

        $this->assertEquals(8, count($materials[9]));
        $this->assertEquals('10', $materials[9]['id']);
        $this->assertEquals('1', $materials[9]['course']);
        $this->assertEquals('tenthlm', $materials[9]['title']);
        $this->assertEquals('firstCourse', $materials[9]['courseTitle']);
        $this->assertEquals(0, count($materials[9]['instructors']));
        $this->assertNotEmpty($materials[9]['startDate']);
        $this->assertNotEmpty($materials[9]['endDate']);
        $this->assertTrue($materials[9]['isBlanked']);
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
        $this->assertCount(10, $materials, 'All materials returned');
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
        $this->assertCount(10, $materials, 'All materials returned');
    }

    protected function getMaterials($userId, $before = null, $after = null)
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
        $this->createJsonRequest(
            'GET',
            $url,
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            $this->fail("Unable to load url: {$url}");
        }

        $this->assertJsonResponse($response, Response::HTTP_OK);
        return json_decode($response->getContent(), true)['userMaterials'];
    }
}
