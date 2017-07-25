<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * ApplicationConfig API endpoint Test.
 * @group api_3
 */
class ApplicationConfigTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'applicationConfigs';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadApplicationConfigData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'value' => ['value', $this->getFaker()->text],
            'name' => ['name', $this->getFaker()->text(100)],
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
            'ids' => [[1, 2], ['id' => [2, 3]]],
            'name' => [[1], ['name' => 'second name']],
            'value' => [[2], ['value' => 'third value']],
        ];
    }
}
