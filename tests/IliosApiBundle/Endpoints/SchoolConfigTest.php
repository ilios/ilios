<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * SchoolConfig API endpoint Test.
 * @group api_5
 */
class SchoolConfigTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'schoolConfigs';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadSchoolData',
            'Tests\CoreBundle\Fixture\LoadSchoolConfigData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'value' => ['value', $this->getFaker()->text(100)],
            'name' => ['name', $this->getFaker()->text(50)],
            'school' => ['school', 2],
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
            'name' => [[1], ['name' => 'second config']],
            'value' => [[2], ['value' => 'third value']],
            'school' => [[2], ['school' => 2]],
        ];
    }
}
