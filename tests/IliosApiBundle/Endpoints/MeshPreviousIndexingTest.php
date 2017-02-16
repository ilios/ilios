<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * MeshPreviousIndexing API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class MeshPreviousIndexingTest extends AbstractTest
{
    protected $testName =  'meshpreviousindexing';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadMeshPreviousIndexingData',
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
            'descriptor' => ['descriptor', $this->getFaker()->text],
            'previousIndexing' => ['previousIndexing', $this->getFaker()->text],
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
            'descriptor' => [[0], ['filters[descriptor]' => 'test']],
            'previousIndexing' => [[0], ['filters[previousIndexing]' => 'test']],
        ];
    }

}