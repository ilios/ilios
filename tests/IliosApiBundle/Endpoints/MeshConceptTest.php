<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * MeshConcept API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_5
 */
class MeshConceptTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'meshConcepts';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadMeshConceptData',
            'Tests\CoreBundle\Fixture\LoadMeshTermData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'name' => ['name', $this->getFaker()->text],
            'id' => ['id', $this->getFaker()->word, $skipped = true],
            'umlsUid' => ['umlsUid', $this->getFaker()->text(9)],
            'preferred' => ['preferred', false],
            'scopeNote' => ['scopeNote', $this->getFaker()->text],
            'casn1Name' => ['casn1Name', $this->getFaker()->text(120)],
            'registryNumber' => ['registryNumber', $this->getFaker()->text(20)],
            'semanticTypes' => ['semanticTypes', [1]],
            'terms' => ['terms', [1]],
            'descriptors' => ['descriptors', [1], $skipped = true],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest()
    {
        return [
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
            'id' => [[0], ['id' => '1']],
            'ids' => [[0, 1], ['id' => ['1', '2']]],
            'name' => [[1], ['name' => 'second concept']],
            'umlsUid' => [[0], ['umlsUid' => 'umlsUid1']],
            'preferred' => [[0], ['preferred' => true]],
            'notPreferred' => [[1], ['preferred' => false]],
            'scopeNote' => [[0], ['scopeNote' => 'first scopeNote']],
            'casn1Name' => [[1], ['casn1Name' => 'second casn']],
            'registryNumber' => [[1], ['registryNumber' => 'abcd']],
            'semanticTypes' => [[0], ['semanticTypes' => [1]]],
            'terms' => [[0], ['terms' => [1]]],
            'descriptors' => [[0, 1], ['descriptors' => ['abc1']]],
        ];
    }

    protected function getTimeStampFields()
    {
        return ['updatedAt', 'createdAt'];
    }

    public function testPostMeshConceptTerm()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'concepts', 'meshTerms', 'terms');
    }
}
