<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * Permission API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class PermissionTest extends AbstractTest
{
    protected $testName =  'permission';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadPermissionData',
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
            'user' => ['user', $this->getFaker()->text],
            'canRead' => ['canRead', false],
            'canWrite' => ['canWrite', false],
            'tableRowId' => ['tableRowId', $this->getFaker()->randomDigit],
            'tableName' => ['tableName', $this->getFaker()->text],
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
            'user' => [[0], ['filters[user]' => 'test']],
            'canRead' => [[0], ['filters[canRead]' => false]],
            'canWrite' => [[0], ['filters[canWrite]' => false]],
            'tableRowId' => [[0], ['filters[tableRowId]' => 1]],
            'tableName' => [[0], ['filters[tableName]' => 'test']],
        ];
    }

}