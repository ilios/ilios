<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * AamcMethod API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class AamcMethodTest extends AbstractEndpointTest
{
    protected $testName =  'aamcmethod';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadAamcMethodData',
            'Tests\CoreBundle\Fixture\LoadSessionTypeData',
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
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnliesToTest()
    {
        return [
            'id' => ['id', 'AM001', 'AMBlank'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 'AM001']],
            'description' => [[1], ['description' => 'filterable description']],
            'sessionTypes' => [[0], ['sessionTypes' => [1]]],
        ];
    }

}