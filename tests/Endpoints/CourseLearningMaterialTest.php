<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use PHPUnit\Framework\Attributes\Group;
use App\Entity\CourseLearningMaterial;
use App\Tests\Fixture\LoadCourseLearningMaterialData;
use App\Tests\Fixture\LoadMeshDescriptorData;

use function array_key_exists;
use function date_format;
use function is_null;

/**
 * CourseLearningMaterial API endpoint Test.
 */
#[Group('api_1')]
class CourseLearningMaterialTest extends AbstractReadWriteEndpoint
{
    protected string $testName =  'courseLearningMaterials';

    protected function getFixtures(): array
    {
        return [
            LoadCourseLearningMaterialData::class,
            LoadMeshDescriptorData::class,
        ];
    }

    public static function putsToTest(): array
    {
        return [
            'notes' => ['notes', 'needs more salt'],
            'emptyNotes' => ['notes', ''],
            'nullNotes' => ['notes', null],
            'required' => ['required', false],
            'publicNotes' => ['publicNotes', false],
            'course' => ['course', 4],
            'learningMaterial' => ['learningMaterial', 2],
            'meshDescriptors' => ['meshDescriptors', ['abc3']],
            'position' => ['position', 2],
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
            'ids' => [[1, 2], ['id' => [2, 3]]],
            'missingId' => [[], ['id' => 99]],
            'missingIds' => [[], ['id' => [99]]],
            'notes' => [[2], ['notes' => 'third note']],
            'notRequired' => [[1], ['required' => false]],
            'required' => [[0, 2, 3, 4, 5, 6, 7, 8, 9], ['required' => true]],
            'notPublicNotes' => [[2], ['publicNotes' => false]],
            'publicNotes' => [[0, 1, 3, 4, 5, 6, 7, 8, 9], ['publicNotes' => true]],
            'course' => [[2], ['course' => 4]],
            'learningMaterial' => [[1], ['learningMaterial' => 2]],
            // 'meshDescriptors' => [[0, 2], ['meshDescriptors' => ['abc1']]], // skipped
            'position' => [[1], ['position' => 1]],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[1, 2], ['ids' => [2, 3]]];
        $filters['missingIds'] = [[], ['ids' => [99]]];

        return $filters;
    }

    /**
     * TOTAL GROSSNESS!
     * get the expected fixture from the repo, then correct
     * the expected start- and end-dates by overriding them.
     * @todo load fixtures upstream without regenerating them [ST/JJ 2021/09/19].
     */
    protected function fixDatesInExpectedData(array $expected): array
    {
        $ref = 'courseLearningMaterials' . $expected['id'];
        if ($this->fixtures->hasReference($ref, CourseLearningMaterial::class)) {
            $fixture = $this->fixtures->getReference($ref, CourseLearningMaterial::class);
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

    public function testGraphQLIncludedData(): void
    {
        $loader = $this->getDataLoader();
        $data = $loader->getOne();

        $this->createGraphQLRequest(
            json_encode([
                'query' =>
                    "query { courseLearningMaterials(id: {$data['id']}) " .
                    "{ id, course { id, title }, learningMaterial { id } }}",
            ]),
            $this->createJwtForRootUser($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();

        $this->assertGraphQLResponse($response);

        $content = json_decode($response->getContent());

        $this->assertIsObject($content->data);
        $this->assertIsArray($content->data->courseLearningMaterials);

        $result = $content->data->courseLearningMaterials;
        $this->assertCount(1, $result);

        $clm = $result[0];
        $this->assertTrue(property_exists($clm, 'id'));
        $this->assertEquals($data['id'], $clm->id);
        $this->assertTrue(property_exists($clm, 'course'));
        $this->assertTrue(property_exists($clm->course, 'id'));
        $this->assertTrue(property_exists($clm->course, 'title'));
        $this->assertEquals($data['course'], $clm->course->id);
        $this->assertEquals('firstCourse', $clm->course->title);

        $this->assertTrue(property_exists($clm, 'learningMaterial'));
        $this->assertTrue(property_exists($clm->learningMaterial, 'id'));
        $this->assertEquals($data['learningMaterial'], $clm->learningMaterial->id);
    }
}
