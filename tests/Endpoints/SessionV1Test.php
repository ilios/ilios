<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\ReadEndpointTest;

/**
 * Session API V1 endpoint Test.
 * @group api_2
 */
class SessionV1Test extends ReadEndpointTest
{
    protected $testName =  'sessions';

    protected $apiVersion = 'v1';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadSessionData',
            'App\Tests\Fixture\LoadTermData',
            'App\Tests\Fixture\LoadSessionDescriptionData',
            'App\Tests\Fixture\LoadSessionLearningMaterialData',
            'App\Tests\Fixture\LoadOfferingData',
            'App\Tests\Fixture\LoadSessionLearningMaterialData',
            'App\Tests\Fixture\LoadCourseLearningMaterialData',
            'App\Tests\Fixture\LoadLearningMaterialStatusData',
            'App\Tests\Fixture\LoadIlmSessionData',
            'App\Tests\Fixture\LoadSessionObjectiveData',
            'App\Tests\Fixture\LoadCourseObjectiveData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function testGetOne()
    {
        $sessionData = $this->getDataLoader()->getOne();
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $v1Session = $this->getOne($endpoint, $responseKey, $sessionData['id']);
        $v2Session = $this->getOne($endpoint, $responseKey, $sessionData['id'], 'v2');
        $sessionObjective = $this->getOne(
            'sessionobjectives',
            'sessionObjectives',
            $v2Session['sessionObjectives'][0],
            'v2'
        );
        $this->assertEquals($v2Session['id'], $v1Session['id']);
        $this->assertEquals($v2Session['title'], $v1Session['title']);
        $this->assertEquals($v2Session['attireRequired'], $v1Session['attireRequired']);
        $this->assertEquals($v2Session['supplemental'], $v1Session['supplemental']);
        $this->assertEquals($v2Session['publishedAsTbd'], $v1Session['publishedAsTbd']);
        $this->assertEquals($v2Session['published'], $v1Session['published']);
        $this->assertEquals($v2Session['instructionalNotes'], $v1Session['instructionalNotes']);
        $this->assertEquals($v2Session['updatedAt'], $v1Session['updatedAt']);
        $this->assertEquals($v2Session['sessionType'], $v1Session['sessionType']);
        $this->assertEquals($v2Session['course'], $v1Session['course']);
        $this->assertEquals($v2Session['terms'], $v1Session['terms']);
        $this->assertEquals($v2Session['meshDescriptors'], $v1Session['meshDescriptors']);
        $this->assertEquals($v2Session['sessionDescription'], $v1Session['sessionDescription']);
        $this->assertEquals($v2Session['administrators'], $v1Session['administrators']);
        $this->assertEquals($v2Session['offerings'], $v1Session['offerings']);
        $this->assertEquals($v2Session['prerequisites'], $v1Session['prerequisites']);
        $this->assertEquals(count($v2Session['sessionObjectives']), count($v1Session['objectives']));
        $this->assertEquals($sessionObjective['objective'], $v1Session['objectives'][0]);
    }

    /**
     * @inheritDoc
     */
    public function testGetAll()
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $v1url = $this->getUrl(
            $this->kernelBrowser,
            'ilios_api_getall',
            ['version' => $this->apiVersion, 'object' => $endpoint]
        );
        $v2url = $this->getUrl(
            $this->kernelBrowser,
            'ilios_api_getall',
            ['version' => 'v2', 'object' => $endpoint]
        );
        $this->createJsonRequest(
            'GET',
            $v1url,
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $v1Response = $this->kernelBrowser->getResponse();

        $this->createJsonRequest(
            'GET',
            $v2url,
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $v2Response = $this->kernelBrowser->getResponse();

        $v1Data = json_decode($v1Response->getContent(), true)[$responseKey];
        $v2Data = json_decode($v2Response->getContent(), true)[$responseKey];

        $this->assertNotEmpty($v1Data);
        $this->assertEquals(count($v2Data), count($v1Data));
        $v1Ids = array_column($v1Data, 'id');
        $v2Ids = array_column($v1Data, 'id');
        $this->assertEquals($v2Ids, $v1Ids);
    }
}
