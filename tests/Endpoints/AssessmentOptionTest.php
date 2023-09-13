<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadAssessmentOptionData;
use App\Tests\Fixture\LoadSessionTypeData;

/**
 * AssessmentOption API endpoint Test.
 * @group api_4
 */
class AssessmentOptionTest extends AbstractReadWriteEndpoint
{
    protected string $testName =  'assessmentOptions';

    protected function getFixtures(): array
    {
        return [
            LoadAssessmentOptionData::class,
            LoadSessionTypeData::class
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest(): array
    {
        return [
            'name' => ['name', 'lorem ipsum'],
            'sessionTypes' => ['sessionTypes', [2, 3], $skipped = true],
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
            'name' => [[1], ['name' => 'second option']],
            'sessionTypes' => [[0], ['sessionTypes' => [1]]],
        ];
    }
    public function graphQLFiltersToTest(): array
    {
        $filters = $this->filtersToTest();
        $filters['ids'] = [[0, 1], ['ids' => [1, 2]]];

        return $filters;
    }

    public function testPutForAllData()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        foreach ($all as $key => $data) {
            $data['name'] = 'text' . $key;

            $this->putTest($data, $data, $data['id']);
        }
    }

    public function testPatchForAllDataJsonApi()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        foreach ($all as $key => $data) {
            $data['name'] = 'text' . $key;
            $jsonApiData = $dataLoader->createJsonApi($data);
            $this->patchJsonApiTest($data, $jsonApiData);
        }
    }
}
