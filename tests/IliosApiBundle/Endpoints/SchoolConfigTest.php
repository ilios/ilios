<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * SchoolConfig API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class SchoolConfigTest extends AbstractEndpointTest
{
    protected $testName =  'schoolconfigs';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadSchoolConfigData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'name' => ['name', $this->getFaker()->text],
            'value' => ['value', $this->getFaker()->text],
            'school' => ['school', $this->getFaker()->text],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnliesToTest()
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
            'name' => [[0], ['name' => 'test']],
            'value' => [[0], ['value' => 'test']],
            'school' => [[0], ['school' => 'test']],
        ];
    }

}