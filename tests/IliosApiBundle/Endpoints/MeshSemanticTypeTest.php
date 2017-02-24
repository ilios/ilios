<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * MeshSemanticType API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class MeshSemanticTypeTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'meshSemanticTypes';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadMeshSemanticTypeData',
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
            'concepts' => ['concepts', [2]],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnliesToTest()
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
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'name' => [[1], ['name' => 'second type']],
            'concepts' => [[0], ['concepts' => [1]], $skipped = true],
        ];
    }

    protected function getTimeStampFields()
    {
        return ['createdAt', 'updatedAt'];
    }

    public function testPostMeshSemanticTypeConcept()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'semanticTypes', 'meshConcepts', 'concepts');
    }
}
