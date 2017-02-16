<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * MeshSemanticType API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class MeshSemanticTypeTest extends AbstractTest
{
    protected $testName =  'meshsemantictype';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadMeshSemanticTypeData',
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
            'concepts' => ['concepts', [1]],
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
            'name' => [[0], ['filters[name]' => 'test']],
            'createdAt' => [[0], ['filters[createdAt]' => 'test']],
            'updatedAt' => [[0], ['filters[updatedAt]' => 'test']],
            'concepts' => [[0], ['filters[concepts]' => [1]]],
        ];
    }

}