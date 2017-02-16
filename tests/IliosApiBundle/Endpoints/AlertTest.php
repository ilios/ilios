<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * Alert API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class AlertTest extends AbstractTest
{
    protected $testName =  'alert';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadAlertData',
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
            'tableRowId' => ['tableRowId', $this->getFaker()->randomDigit],
            'tableName' => ['tableName', $this->getFaker()->text],
            'additionalText' => ['additionalText', $this->getFaker()->text],
            'dispatched' => ['dispatched', false],
            'changeTypes' => ['changeTypes', [1]],
            'instigators' => ['instigators', [1]],
            'recipients' => ['recipients', [1]],
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
            'tableRowId' => [[0], ['filters[tableRowId]' => 1]],
            'tableName' => [[0], ['filters[tableName]' => 'test']],
            'additionalText' => [[0], ['filters[additionalText]' => 'test']],
            'dispatched' => [[0], ['filters[dispatched]' => false]],
            'changeTypes' => [[0], ['filters[changeTypes]' => [1]]],
            'instigators' => [[0], ['filters[instigators]' => [1]]],
            'recipients' => [[0], ['filters[recipients]' => [1]]],
        ];
    }

}