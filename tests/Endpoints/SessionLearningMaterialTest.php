<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadLearningMaterialData;
use App\Tests\Fixture\LoadMeshDescriptorData;
use App\Tests\Fixture\LoadSessionData;
use App\Tests\Fixture\LoadSessionLearningMaterialData;
use App\Tests\ReadWriteEndpointTest;

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
            'required' => [[0], ['required' => true]],
            'notRequired' => [[1, 2, 3, 4, 5, 6, 7], ['required' => false]],
            'publicNotes' => [[1, 2, 3, 4, 5, 6, 7], ['publicNotes' => true]],
            'notPublicNotes' => [[0], ['publicNotes' => false]],
            'session' => [[0], ['session' => 1]],
            'learningMaterial' => [[0], ['learningMaterial' => 1]],
            'meshDescriptors' => [[1, 2, 3, 4, 5, 6, 7], ['meshDescriptors' => ['abc2']]],
            'position' => [[1, 2, 3, 4, 5, 6, 7], ['position' => 0]],
            'school' => [[0, 1, 2, 3, 4, 5, 6, 7], ['schools' => 1]],
            'schools' => [[0, 1, 2, 3, 4, 5, 6, 7], ['schools' => [1]]],
        ];
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
