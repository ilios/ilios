<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use PHPUnit\Framework\Attributes\Group;
use App\Tests\Fixture\LoadCourseLearningMaterialData;
use App\Tests\Fixture\LoadIlmSessionData;
use App\Tests\Fixture\LoadOfferingData;
use App\Tests\Fixture\LoadSessionLearningMaterialData;
use App\Tests\Fixture\LoadUserData;
use Symfony\Component\HttpFoundation\Response;

/**
 * UsermaterialsTest API endpoint Test.
 */
#[Group('api_3')]
final class UsermaterialsTest extends AbstractEndpoint
{
    protected function getFixtures(): array
    {
        return [
            LoadOfferingData::class,
            LoadIlmSessionData::class,
            LoadUserData::class,
            LoadSessionLearningMaterialData::class,
            LoadCourseLearningMaterialData::class,
        ];
    }

    public function testGetAllMaterials(): void
    {
        $userId = 5;
        $materials = $this->getMaterials($userId);
        $this->assertCount(9, $materials, 'All expected materials returned');

        $this->assertEquals(18, count($materials[0]));
        $this->assertEquals('1', $materials[0]['id']);
        $this->assertEquals('1', $materials[0]['sessionLearningMaterial']);
        $this->assertEquals('1', $materials[0]['session']);
        $this->assertTrue($materials[0]['required']);
        $this->assertEquals('1', $materials[0]['position']);
        $this->assertMatchesRegularExpression('/^firstlm/', $materials[0]['title']);
        $this->assertMatchesRegularExpression('/^desc1/', $materials[0]['description']);
        $this->assertMatchesRegularExpression('/^author1/', $materials[0]['originalAuthor']);
        $this->assertMatchesRegularExpression('/^citation1/', $materials[0]['citation']);
        $this->assertEquals('citation', $materials[0]['mimetype']);
        $this->assertMatchesRegularExpression('/^session1Title/', $materials[0]['sessionTitle']);
        $this->assertEquals('1', $materials[0]['course']);
        $this->assertMatchesRegularExpression('/^firstCourse/', $materials[0]['courseTitle']);
        $this->assertEquals(2016, $materials[0]['courseYear']);
        $this->assertEquals('first', $materials[0]['courseExternalId']);
        $this->assertEquals('2016-09-08T15:00:00+00:00', $materials[0]['firstOfferingDate']);
        $this->assertEquals(['disnom (them)'], $materials[0]['instructors']);
        $this->assertFalse($materials[0]['isBlanked']);

        $this->assertEquals(17, count($materials[1]));
        $this->assertEquals('1', $materials[1]['id']);
        $this->assertEquals('1', $materials[1]['course']);
        $this->assertArrayNotHasKey('session', $materials[1]);
        $this->assertFalse($materials[1]['isBlanked']);

        $this->assertEquals(19, count($materials[2]));
        $this->assertEquals('3', $materials[2]['id']);
        $this->assertEquals('1', $materials[2]['course']);
        $this->assertEquals('2016-09-04T00:00:00+00:00', $materials[2]['firstOfferingDate']);
        $this->assertArrayNotHasKey('session', $materials[2]);
        $this->assertFalse($materials[2]['isBlanked']);

        $this->assertEquals(20, count($materials[3]));
        $this->assertNotEmpty($materials[3]['startDate']);
        $this->assertFalse($materials[3]['isBlanked']);

        $this->assertEquals(12, count($materials[4]));
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

        $this->assertEquals(20, count($materials[5]));
        $this->assertNotEmpty($materials[5]['endDate']);
        $this->assertFalse($materials[5]['isBlanked']);

        $this->assertEquals(12, count($materials[6]));
        $this->assertEquals('8', $materials[6]['id']);
        $this->assertEquals('1', $materials[6]['course']);
        $this->assertEquals('eighthlm', $materials[6]['title']);
        $this->assertEquals('firstCourse', $materials[6]['courseTitle']);
        $this->assertEquals(0, count($materials[6]['instructors']));
        $this->assertNotEmpty($materials[6]['endDate']);
        $this->assertTrue($materials[6]['isBlanked']);

        $this->assertEquals(21, count($materials[7]));
        $this->assertNotEmpty($materials[7]['startDate']);
        $this->assertNotEmpty($materials[7]['endDate']);
        $this->assertFalse($materials[7]['isBlanked']);

        $this->assertEquals(13, count($materials[8]));
        $this->assertNotEmpty($materials[8]['startDate']);
        $this->assertNotEmpty($materials[8]['endDate']);
        $this->assertTrue($materials[8]['isBlanked']);
    }

    public function testGetAllMaterialsAsStudent(): void
    {
        $userId = 5;
        $materials = $this->getMaterials($userId, null, null, $userId);
        $this->assertCount(9, $materials, 'All expected materials returned');
        $materialIds = array_column($materials, 'id');
        $this->assertFalse(in_array('2', $materialIds), 'Draft material was filtered out.');
    }

    public function testWhenViewingAnotherUsersEventsOnlyPublishedShows(): void
    {
        $userId = 5;
        $materials = $this->getMaterials($userId);
        $this->assertCount(9, $materials, 'All expected materials returned');
        $materialIds = array_column($materials, 'id');
        $this->assertFalse(in_array('2', $materialIds), 'Draft material was filtered out.');
    }

    public function testGetMaterialsBeforeTheBeginningOfTime(): void
    {
        $userId = 5;
        $materials = $this->getMaterials($userId, 0);

        $this->assertCount(0, $materials, 'No materials returned');
    }

    public function testGetMaterialsAfterTheBeginningOfTime(): void
    {
        $userId = 5;
        $materials = $this->getMaterials($userId, null, 0);
        $this->assertCount(9, $materials, 'All materials returned');
    }

    public function testGetMaterialsAfterTheEndOfTime(): void
    {
        $userId = 5;
        $materials = $this->getMaterials($userId, null, 2051233745);
        $this->assertCount(0, $materials, 'No materials returned');
    }

    public function testGetMaterialsBeforeTheEndOfTime(): void
    {
        $userId = 5;
        $materials = $this->getMaterials($userId, 2051233745);
        $this->assertCount(9, $materials, 'All materials returned');
    }

    public function testAccessDenied(): void
    {
        $this->runAccessDeniedTest();
    }

    public function testAccessDeniedWithServiceToken(): void
    {
        $jwt = $this->createJwtFromServiceTokenWithWriteAccessInAllSchools(
            $this->kernelBrowser,
            $this->fixtures
        );
        $this->runAccessDeniedTest($jwt, Response::HTTP_FORBIDDEN);
    }

    protected function getMaterials(int $userId, ?int $before = null, ?int $after = null, ?int $authUser = null): array
    {
        $parameters = [
            'version' => $this->apiVersion,
            'id' => $userId,
        ];
        if (null !== $before) {
            $parameters['before'] = $before;
        }
        if (null !== $after) {
            $parameters['after'] = $after;
        }
        $url = $this->getUrl(
            $this->kernelBrowser,
            'app_api_usermaterial_getmaterials',
            $parameters
        );

        $token = isset($authUser) ?
            $this->createJwtFromUserId($this->kernelBrowser, $authUser) :
            $this->createJwtForRootUser($this->kernelBrowser);
        $this->createJsonRequest(
            'GET',
            $url,
            null,
            $token
        );

        $response = $this->kernelBrowser->getResponse();

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            $this->fail("Unable to load url: $url");
        }

        $this->assertJsonResponse($response, Response::HTTP_OK);
        return json_decode($response->getContent(), true)['userMaterials'];
    }

    protected function runAccessDeniedTest(
        ?string $jwt = null,
        int $expectedResponseCode = Response::HTTP_UNAUTHORIZED
    ): void {
        $parameters = [
            'version' => $this->apiVersion,
            'id' => 99,
        ];
        $url = $this->getUrl(
            $this->kernelBrowser,
            'app_api_usermaterial_getmaterials',
            $parameters
        );

        $this->createJsonRequest(
            'GET',
            $url,
            null,
            $jwt
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, $expectedResponseCode);
    }
}
