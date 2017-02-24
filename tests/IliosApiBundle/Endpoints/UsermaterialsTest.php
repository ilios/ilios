<?php

namespace Tests\IliosApiBundle\Endpoints;

use Symfony\Component\HttpFoundation\Response;
use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * UsermaterialsTest API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
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

    public function testGetMaterials()
    {
        $userId = 5;
        $materials = $this->getMaterials($userId);
        $this->assertCount(4, $materials, 'All expected materials returned');
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

        $this->assertEquals('1', $materials[1]['id']);
        $this->assertEquals('1', $materials[1]['course']);
        $this->assertFalse(array_key_exists('session', $materials[1]));
        $this->assertEquals('2', $materials[2]['id']);
        $this->assertEquals('1', $materials[2]['course']);
        $this->assertEquals('2016-09-04T00:00:00+00:00', $materials[2]['firstOfferingDate']);
        $this->assertFalse(array_key_exists('session', $materials[2]));
        $this->assertEquals('3', $materials[3]['id']);
        $this->assertEquals('1', $materials[3]['course']);
        $this->assertEquals('2016-09-04T00:00:00+00:00', $materials[2]['firstOfferingDate']);
        $this->assertFalse(array_key_exists('session', $materials[3]));
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
        $this->assertCount(4, $materials, 'All materials returned');
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
        $this->assertCount(4, $materials, 'All materials returned');
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
