<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * MeshTerm API endpoint Test.
 * @group api_3
 */
class MeshTermTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'meshTerms';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadMeshTermData',
            'Tests\CoreBundle\Fixture\LoadMeshConceptData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'meshTermUid' => ['meshTermUid', $this->getFaker()->text(9)],
            'name' => ['name', $this->getFaker()->text],
            'lexicalTag' => ['lexicalTag', $this->getFaker()->text(12)],
            'conceptPreferred' => ['conceptPreferred', false],
            'recordPreferred' => ['recordPreferred', true],
            'permuted' => ['permuted', false],
            'concepts' => ['concepts', [2]],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest()
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
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'meshTermUid' => [[1], ['meshTermUid' => 'uid2']],
            'name' => [[1], ['name' => 'second term']],
            'lexicalTag' => [[0], ['lexicalTag' => 'first tag']],
            'conceptPreferred' => [[0], ['conceptPreferred' => true]],
            'conceptNotPreferred' => [[1], ['conceptPreferred' => false]],
            'recordPreferred' => [[1], ['recordPreferred' => true]],
            'recordNotPreferred' => [[0], ['recordPreferred' => false]],
            'permuted' => [[0], ['permuted' => true]],
            'notPermuted' => [[1], ['permuted' => false]],
            'concepts' => [[0, 1], ['concepts' => [1]]],
        ];
    }

    public function getTimeStampFields()
    {
        return ['createdAt', 'updatedAt'];
    }

    public function testPostMeshTermConcept()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'terms', 'meshConcepts', 'concepts');
    }
}
