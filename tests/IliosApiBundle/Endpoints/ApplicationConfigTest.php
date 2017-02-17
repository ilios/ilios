<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * ApplicationConfig API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class ApplicationConfigTest extends AbstractTest
{
    protected $testName =  'applicationconfig';

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
        ];
    }

}