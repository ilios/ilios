<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadLearningMaterialData;
use App\Tests\Fixture\LoadMeshDescriptorData;
use App\Tests\Fixture\LoadOfferingData;
use App\Tests\Fixture\LoadSessionData;
use App\Tests\Fixture\LoadSessionLearningMaterialData;
use App\Tests\Fixture\LoadUserData;
use App\Tests\ReadWriteEndpointTest;

use Symfony\Component\HttpFoundation\Response;
use function date_format;
use function is_null;

/**
 * SessionLearningMaterial API endpoint Test.
 * @group api_1
 */
class SessionLearningMaterialTest extends ReadWriteEndpointTest
{
    protected string $testName = 'sessionLearningMaterials';

    protected function getFixtures(): array
    {
        return [
            LoadSessionLearningMaterialData::class,
            LoadSessionData::class,
            LoadLearningMaterialData::class,
            LoadMeshDescriptorData::class,
            LoadOfferingData::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest(): array
    {
        return [
            'notes' => ['notes', 'something else'],
            'emptyNotees' => ['notes', ''],
            'nullNotes' => ['notes', null],
            'required' => ['required', false],
            'publicNotes' => ['publicNotes', true],
            'session' => ['session', 3],
            'learningMaterial' => ['learningMaterial', 3],
            'meshDescriptors' => ['meshDescriptors', ['abc2']],
            'position' => ['position', 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest(): array
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'notes' => [[1], ['notes' => 'second slm']],
            'required' => [[0, 8], ['required' => true]],
            'notRequired' => [[1, 2, 3, 4, 5, 6, 7], ['required' => false]],
            'publicNotes' => [[1, 2, 3, 4, 5, 6, 7, 8], ['publicNotes' => true]],
            'notPublicNotes' => [[0], ['publicNotes' => false]],
            'session' => [[0], ['session' => 1]],
            'learningMaterial' => [[0], ['learningMaterial' => 1]],
            'meshDescriptors' => [[1, 2, 3, 4, 5, 6, 7], ['meshDescriptors' => ['abc2']]],
            'position' => [[1, 2, 3, 4, 5, 6, 7], ['position' => 0]],
            'school' => [[0, 1, 2, 3, 4, 5, 6, 7, 8], ['schools' => 1]],
            'schools' => [[0, 1, 2, 3, 4, 5, 6, 7, 8], ['schools' => [1]]],
        ];
    }

    public function testCanViewOneAsLearnerInSessionWhenAvailable()
    {
        $url = $this->getUrl(
            $this->kernelBrowser,
            "app_api_sessionlearningmaterials_getone",
            ['version' => $this->apiVersion, 'id' => 1]
        );
        $this->createJsonRequest(
            'GET',
            $url,
            null,
            $this->getTokenForUser($this->kernelBrowser, 5)
        );
        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
        $response = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('sessionLearningMaterials', $response);
        $this->assertCount(1, $response['sessionLearningMaterials']);

        $data = $response['sessionLearningMaterials'][0];

        $this->assertEquals(1, $data['id']);
        $this->assertArrayNotHasKey('notes', $data);
    }

    public function testCanViewAllAsLearnerInSessionWhenAvailable()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_sessionlearningmaterials_getall",
                ['version' => $this->apiVersion]
            ),
            null,
            $this->getTokenForUser($this->kernelBrowser, 5)
        );
        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
        $responses = json_decode($response->getContent(), true)['sessionLearningMaterials'];

        $this->assertCount(5, $responses);
        $this->assertEquals(1, $responses[0]['id']);
        $this->assertArrayNotHasKey('notes', $responses[0]);
        $this->assertEquals(2, $responses[1]['id']);
        $this->assertEquals('second slm', $responses[1]['notes']);
        $this->assertEquals(3, $responses[2]['id']);
        $this->assertEquals('third slm', $responses[2]['notes']);
        $this->assertEquals(5, $responses[3]['id']);
        $this->assertEquals('fifth slm', $responses[3]['notes']);
        $this->assertEquals(7, $responses[4]['id']);
        $this->assertEquals('seventh slm', $responses[4]['notes']);
    }

    /**
     * TOTAL GROSSNESS!
     * get the expected fixture from the repo, then correct
     * the expected start- and end-dates by overriding them.
     * @todo load fixtures upstream without regenerating them [ST/JJ 2021/09/18].
     */
    protected function fixDatesInExpectedData(array $expected): array
    {
        $ref = 'sessionLearningMaterials' . $expected['id'];
        if ($this->fixtures->hasReference($ref)) {
            $fixture = $this->fixtures->getReference($ref);
            $startDate = $fixture->getStartDate();
            $endDate = $fixture->getEndDate();
            $expected['startDate'] = is_null($startDate) ? null : date_format($startDate, 'c');
            $expected['endDate'] = is_null($endDate) ? null : date_format($endDate, 'c');
        }

        return $expected;
    }

    protected function compareData(array $expected, array $result)
    {
        $expected = $this->fixDatesInExpectedData($expected);
        if (is_null($expected['startDate'])) {
            $this->assertFalse(array_key_exists('startDate', $result));
            unset($expected['startDate']);
        }

        if (is_null($expected['endDate'])) {
            $this->assertFalse(array_key_exists('endDate', $result));
            unset($expected['endDate']);
        }

        parent::compareData($expected, $result);
    }

    protected function compareGraphQLData(array $expected, object $result): void
    {
        $expected = $this->fixDatesInExpectedData($expected);

        parent::compareGraphQLData($expected, $result);
    }
}
