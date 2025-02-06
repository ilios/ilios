<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use PHPUnit\Framework\Attributes\Group;
use App\Entity\SessionLearningMaterial;
use App\Tests\Fixture\LoadLearningMaterialData;
use App\Tests\Fixture\LoadMeshDescriptorData;
use App\Tests\Fixture\LoadOfferingData;
use App\Tests\Fixture\LoadSessionData;
use App\Tests\Fixture\LoadSessionLearningMaterialData;
use Symfony\Component\HttpFoundation\Response;

use function date_format;
use function is_null;

/**
 * SessionLearningMaterial API endpoint Test.
 */
#[Group('api_1')]
class SessionLearningMaterialTest extends AbstractReadWriteEndpoint
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

    public static function putsToTest(): array
    {
        return [
            'notes' => ['notes', 'something else'],
            'emptyNotes' => ['notes', ''],
            'nullNotes' => ['notes', null],
            'required' => ['required', false],
            'publicNotes' => ['publicNotes', true],
            'session' => ['session', 3],
            'learningMaterial' => ['learningMaterial', 3],
            'meshDescriptors' => ['meshDescriptors', ['abc2']],
            'position' => ['position', 99],
        ];
    }

    public static function readOnlyPropertiesToTest(): array
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    public static function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'missingId' => [[], ['id' => 99]],
            'missingIds' => [[], ['id' => [99]]],
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

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[0, 1], ['ids' => [1, 2]]];
        $filters['missingIds'] = [[], ['ids' => [99]]];

        return $filters;
    }

    public function testCanViewOneAsLearnerInSessionWhenAvailable(): void
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
            $this->createJwtFromUserId($this->kernelBrowser, 5)
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

    public function testCanViewAllAsLearnerInSessionWhenAvailable(): void
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_sessionlearningmaterials_getall",
                ['version' => $this->apiVersion]
            ),
            null,
            $this->createJwtFromUserId($this->kernelBrowser, 5)
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

    public function testGraphQLIncludedData(): void
    {
        $loader = $this->getDataLoader();
        $data = $loader->getOne();

        $this->createGraphQLRequest(
            json_encode([
                'query' =>
                    "query { sessionLearningMaterials(id: {$data['id']}) " .
                    "{ id, session { id, title, course { id } }, learningMaterial { id } }}",
            ]),
            $this->createJwtForRootUser($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();

        $this->assertGraphQLResponse($response);

        $content = json_decode($response->getContent());

        $this->assertIsObject($content->data);
        $this->assertIsArray($content->data->sessionLearningMaterials);

        $result = $content->data->sessionLearningMaterials;
        $this->assertCount(1, $result);

        $slm = $result[0];
        $this->assertTrue(property_exists($slm, 'id'));
        $this->assertEquals($data['id'], $slm->id);
        $this->assertTrue(property_exists($slm, 'session'));
        $this->assertTrue(property_exists($slm->session, 'id'));
        $this->assertTrue(property_exists($slm->session, 'title'));
        $this->assertEquals($data['session'], $slm->session->id);
        $this->assertEquals('session1Title', $slm->session->title);

        $this->assertTrue(property_exists($slm, 'learningMaterial'));
        $this->assertTrue(property_exists($slm->learningMaterial, 'id'));
        $this->assertEquals($data['learningMaterial'], $slm->learningMaterial->id);

        $this->assertTrue(property_exists($slm->session, 'course'));
        $this->assertTrue(property_exists($slm->session->course, 'id'));
        $this->assertEquals('1', $slm->session->course->id);
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
        if ($this->fixtures->hasReference($ref, SessionLearningMaterial::class)) {
            $fixture = $this->fixtures->getReference($ref, SessionLearningMaterial::class);
            $startDate = $fixture->getStartDate();
            $endDate = $fixture->getEndDate();
            $expected['startDate'] = is_null($startDate) ? null : date_format($startDate, 'c');
            $expected['endDate'] = is_null($endDate) ? null : date_format($endDate, 'c');
        }

        return $expected;
    }

    protected function compareData(array $expected, array $result): void
    {
        $expected = $this->fixDatesInExpectedData($expected);
        if (array_key_exists('startDate', $expected) && is_null($expected['startDate'])) {
            $this->assertArrayNotHasKey('startDate', $result);
            unset($expected['startDate']);
        }

        if (array_key_exists('endDate', $expected) && is_null($expected['endDate'])) {
            $this->assertArrayNotHasKey('endDate', $result);
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
