<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * MeshConcept API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class MeshConceptTest extends AbstractEndpointTest
{
    protected $testName =  'meshconcept';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadMeshConceptData',
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
            'umlsUid' => ['umlsUid', $this->getFaker()->text],
            'preferred' => ['preferred', false],
            'scopeNote' => ['scopeNote', $this->getFaker()->text],
            'casn1Name' => ['casn1Name', $this->getFaker()->text],
            'registryNumber' => ['registryNumber', $this->getFaker()->text],
            'semanticTypes' => ['semanticTypes', [1]],
            'terms' => ['terms', [1]],
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
            'umlsUid' => [[0], ['filters[umlsUid]' => 'test']],
            'preferred' => [[0], ['filters[preferred]' => false]],
            'scopeNote' => [[0], ['filters[scopeNote]' => 'test']],
            'casn1Name' => [[0], ['filters[casn1Name]' => 'test']],
            'registryNumber' => [[0], ['filters[registryNumber]' => 'test']],
            'semanticTypes' => [[0], ['filters[semanticTypes]' => [1]]],
            'terms' => [[0], ['filters[terms]' => [1]]],
            'createdAt' => [[0], ['filters[createdAt]' => 'test']],
            'updatedAt' => [[0], ['filters[updatedAt]' => 'test']],
            'descriptors' => [[0], ['filters[descriptors]' => [1]]],
        ];
    }

}