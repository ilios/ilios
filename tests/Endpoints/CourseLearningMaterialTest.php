<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadCourseLearningMaterialData;
use App\Tests\Fixture\LoadMeshDescriptorData;
use App\Tests\ReadWriteEndpointTest;

use function array_key_exists;
use function date_format;
use function is_null;

/**
 * CourseLearningMaterial API endpoint Test.
 * @group api_1
 */
class CourseLearningMaterialTest extends ReadWriteEndpointTest
{
    protected string $testName =  'courseLearningMaterials';

    protected function getFixtures(): array
    {
        return [
            LoadCourseLearningMaterialData::class,
            LoadMeshDescriptorData::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest(): array
    {
        return [
            'notes' => ['notes', $this->getFaker()->text()],
            'emptyNotes' => ['notes', ''],
            'nullNotes' => ['notes', null],
            'required' => ['required', false],
            'publicNotes' => ['publicNotes', false],
            'course' => ['course', 4],
            'learningMaterial' => ['learningMaterial', 2],
            'meshDescriptors' => ['meshDescriptors', ['abc3']],
            'position' => ['position', $this->getFaker()->randomDigit()],
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
            'ids' => [[1, 2], ['id' => [2, 3]]],
            'notes' => [[2], ['notes' => 'third note']],
            'notRequired' => [[1], ['required' => false]],
            'required' => [[0, 2, 3, 4, 5, 6, 7, 8, 9], ['required' => true]],
            'notPublicNotes' => [[2], ['publicNotes' => false]],
            'publicNotes' => [[0, 1, 3, 4, 5, 6, 7, 8, 9], ['publicNotes' => true]],
            'course' => [[2], ['course' => 4]],
            'learningMaterial' => [[1], ['learningMaterial' => 2]],
            'meshDescriptors' => [[0, 2], ['meshDescriptors' => ['abc1']], $skipped = true],
            'position' => [[1], ['position' => 1]],
        ];
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
