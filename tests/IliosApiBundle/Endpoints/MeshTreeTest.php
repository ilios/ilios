<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * MeshTree API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class MeshTreeTest extends AbstractTest
{
    protected $testName =  'meshtree';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadMeshTreeData',
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
            'treeNumber' => ['treeNumber', $this->getFaker()->text],
            'descriptor' => ['descriptor', $this->getFaker()->text],
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
            'treeNumber' => [[0], ['filters[treeNumber]' => 'test']],
            'descriptor' => [[0], ['filters[descriptor]' => 'test']],
        ];
    }

}