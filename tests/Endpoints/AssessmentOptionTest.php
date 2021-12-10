<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadAssessmentOptionData;
use App\Tests\Fixture\LoadSessionTypeData;
use App\Tests\ReadWriteEndpointTest;

/**
 * AssessmentOption API endpoint Test.
 * @group api_4
 */
class AssessmentOptionTest extends ReadWriteEndpointTest
{
    protected string $testName =  'assessmentOptions';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            LoadAssessmentOptionData::class,
            LoadSessionTypeData::class
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'name' => ['name', $this->getFaker()->text(18)],
            'sessionTypes' => ['sessionTypes', [2, 3], $skipped = true],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest()
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'name' => [[1], ['name' => 'second option']],
            'sessionTypes' => [[0], ['sessionTypes' => [1]]],
        ];
    }

    public function testPutForAllData()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $faker = $this->getFaker();
        foreach ($all as $key => $data) {
            $data['name'] = $faker->text(10) . $key;

            $this->putTest($data, $data, $data['id']);
        }
    }

    public function testPatchForAllDataJsonApi()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $faker = $this->getFaker();
        foreach ($all as $key => $data) {
            $data['name'] = $faker->text(10) . $key;
            $jsonApiData = $dataLoader->createJsonApi($data);
            $this->patchJsonApiTest($data, $jsonApiData);
        }
    }
}
