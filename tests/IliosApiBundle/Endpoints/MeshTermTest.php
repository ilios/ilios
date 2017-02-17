<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * MeshTerm API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class MeshTermTest extends AbstractEndpointTest
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
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 1]],
            'meshTermUid' => [[0], ['meshTermUid' => 'test']],
            'name' => [[0], ['name' => 'test']],
            'lexicalTag' => [[0], ['lexicalTag' => 'test']],
            'conceptPreferred' => [[0], ['conceptPreferred' => 'test']],
            'recordPreferred' => [[0], ['recordPreferred' => 'test']],
            'permuted' => [[0], ['permuted' => 'test']],
            'printable' => [[0], ['printable' => 'test']],
            'createdAt' => [[0], ['createdAt' => 'test']],
            'updatedAt' => [[0], ['updatedAt' => 'test']],
            'concepts' => [[0], ['concepts' => [1]]],
        ];
    }

}