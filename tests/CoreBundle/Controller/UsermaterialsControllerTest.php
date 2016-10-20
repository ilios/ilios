<?php

namespace Tests\CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;
use DateTime;

/**
 * UserRole controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class UsermaterialsControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Tests\CoreBundle\Fixture\LoadOfferingData',
            'Tests\CoreBundle\Fixture\LoadIlmSessionData',
            'Tests\CoreBundle\Fixture\LoadUserData',
            'Tests\CoreBundle\Fixture\LoadSessionLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadCourseLearningMaterialData'
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
    public function testGetMaterials()
    {
        $userId = 2;
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_usermaterials',
                ['id' => $userId]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $materials = json_decode($response->getContent(), true)['userMaterials'];
        $this->assertCount(1, $materials, 'All expected materials returned');
        $ulm = $materials[0];
        $this->assertEquals('1', $ulm['id']);
        $this->assertEquals('1', $ulm['session']);
        $this->assertFalse($ulm['required']);
        $this->assertRegExp('/^firstlm/', $ulm['title']);
        $this->assertRegExp('/^desc1/', $ulm['description']);
        $this->assertRegExp('/^author1/', $ulm['originalAuthor']);
        $this->assertRegExp('/^citation1/', $ulm['citation']);
        $this->assertEquals('citation', $ulm['mimetype']);
        $this->assertRegExp('/^session1Title/', $ulm['sessionTitle']);
        $this->assertFalse(isset($ulm['courseTitle']));
        $this->assertFalse(isset($ulm['course']));
    }
}