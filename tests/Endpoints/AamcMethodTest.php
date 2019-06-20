<?php

namespace App\Tests\Endpoints;

use App\Tests\ReadWriteEndpointTest;

/**
 * AamcMethod API endpoint Test.
 * @group api_1
 */
class AamcMethodTest extends ReadWriteEndpointTest
{
    protected $testName =  'aamcMethods';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadAamcMethodData',
            'App\Tests\Fixture\LoadSessionTypeData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'description' => ['description', $this->getFaker()->text],
            'sessionTypes' => ['sessionTypes', [1]],
            'id' => ['id', 'NEW1', $skip = true],
            'active' => ['active', false],

        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 'AM001']],
            'ids' => [[0, 1], ['id' => ['AM001', 'AM002']]],
            'description' => [[1], ['description' => 'filterable description']],
            'sessionTypes' => [[0], ['sessionTypes' => [1]]],
            'active' => [[0], ['active' => true]],
            'notActive' => [[1], ['active' => false]],
        ];
    }
}
