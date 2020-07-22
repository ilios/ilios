<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\ReadEndpointTest;

/**
 * ProgramYearSteward API endpoint Test.
 * @group api_2
 */
class ProgramYearStewardTest extends ReadEndpointTest
{
    protected $testName =  'programYearStewards';

    protected $apiVersion = 'v1';
    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadProgramYearStewardData',
            'App\Tests\Fixture\LoadDepartmentData',
            'App\Tests\Fixture\LoadProgramYearData',
            'App\Tests\Fixture\LoadSchoolData'
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
            'department' => [[1], ['department' => 2]],
            'programYear' => [[0, 1], ['programYear' => 1]],
            'school' => [[0, 1], ['school' => 1]],
        ];
    }
}
