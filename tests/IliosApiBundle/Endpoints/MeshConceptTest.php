<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * MeshConcept API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
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
            'id' => [[0], ['id' => 'test']],
            'name' => [[0], ['name' => 'test']],
            'umlsUid' => [[0], ['umlsUid' => 'test']],
            'preferred' => [[0], ['preferred' => false]],
            'scopeNote' => [[0], ['scopeNote' => 'test']],
            'casn1Name' => [[0], ['casn1Name' => 'test']],
            'registryNumber' => [[0], ['registryNumber' => 'test']],
            'semanticTypes' => [[0], ['semanticTypes' => [1]]],
            'terms' => [[0], ['terms' => [1]]],
            'createdAt' => [[0], ['createdAt' => 'test']],
            'updatedAt' => [[0], ['updatedAt' => 'test']],
            'descriptors' => [[0], ['descriptors' => [1]]],
        ];
    }

}