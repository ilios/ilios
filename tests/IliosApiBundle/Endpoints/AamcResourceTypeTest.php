<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * AamcResourceType API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class AamcResourceTypeTest extends AbstractTest
{
    protected $testName =  'aamcresourcetype';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadAamcResourceTypeData',
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
            'description' => ['description', $this->getFaker()->text],
            'terms' => ['terms', [1]],
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
            'id' => [[0], ['filters[id]' => 'test']],
            'title' => [[0], ['filters[title]' => 'test']],
            'description' => [[0], ['filters[description]' => 'test']],
            'terms' => [[0], ['filters[terms]' => [1]]],
        ];
    }

}