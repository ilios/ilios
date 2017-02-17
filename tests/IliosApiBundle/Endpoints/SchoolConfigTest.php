<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * SchoolConfig API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class SchoolConfigTest extends AbstractTest
{
    protected $testName =  'schoolconfig';

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
     *
     * returns an array of field / value pairs to modify
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
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
     *
     * returns an array of field / value pairs that are readOnly
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
     */
    public function readOnliesToTest()
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     *
     * returns an array of filters to test
     * the key for each item is reflected in the failure message
     * the first item is an array of the positions the expected items
     * can be found in the data loader
     * the second item is the filter we are testing
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['filters[id]' => 1]],
            'name' => [[0], ['filters[name]' => 'test']],
            'value' => [[0], ['filters[value]' => 'test']],
            'school' => [[0], ['filters[school]' => 'test']],
        ];
    }

}