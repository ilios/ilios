<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * MeshQualifier API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class MeshQualifierTest extends AbstractTest
{
    protected $testName =  'meshqualifier';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadMeshQualifierData',
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
            'descriptors' => ['descriptors', [1]],
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
            'createdAt' => ['createdAt', 1, 99],
            'updatedAt' => ['updatedAt', 1, 99],
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
            'descriptors' => [[0], ['filters[descriptors]' => [1]]],
        ];
    }

}