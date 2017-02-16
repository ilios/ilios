<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * UserRole API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class UserRoleTest extends AbstractTest
{
    protected $testName =  'userrole';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadUserRoleData',
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
            'title' => ['title', $this->getFaker()->text],
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
            'title' => [[0], ['filters[title]' => 'test']],
        ];
    }

}