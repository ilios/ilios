<?php

namespace Tests\App\Endpoints;

use Tests\App\ReadWriteEndpointTest;

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
            'Tests\AppBundle\Fixture\LoadAamcMethodData',
            'Tests\AppBundle\Fixture\LoadSessionTypeData',
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
        ];
    }
}
