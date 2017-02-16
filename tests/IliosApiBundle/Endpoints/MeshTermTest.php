<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * MeshTerm API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class MeshTermTest extends AbstractTest
{
    protected $testName =  'meshterm';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadMeshTermData',
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
            'meshTermUid' => ['meshTermUid', $this->getFaker()->text],
            'name' => ['name', $this->getFaker()->text],
            'lexicalTag' => ['lexicalTag', $this->getFaker()->text],
            'conceptPreferred' => ['conceptPreferred', $this->getFaker()->text],
            'recordPreferred' => ['recordPreferred', $this->getFaker()->text],
            'permuted' => ['permuted', $this->getFaker()->text],
            'printable' => ['printable', $this->getFaker()->text],
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
            'id' => [[0], ['filters[id]' => 1]],
            'meshTermUid' => [[0], ['filters[meshTermUid]' => 'test']],
            'name' => [[0], ['filters[name]' => 'test']],
            'lexicalTag' => [[0], ['filters[lexicalTag]' => 'test']],
            'conceptPreferred' => [[0], ['filters[conceptPreferred]' => 'test']],
            'recordPreferred' => [[0], ['filters[recordPreferred]' => 'test']],
            'permuted' => [[0], ['filters[permuted]' => 'test']],
            'printable' => [[0], ['filters[printable]' => 'test']],
            'createdAt' => [[0], ['filters[createdAt]' => 'test']],
            'updatedAt' => [[0], ['filters[updatedAt]' => 'test']],
            'concepts' => [[0], ['filters[concepts]' => [1]]],
        ];
    }

}