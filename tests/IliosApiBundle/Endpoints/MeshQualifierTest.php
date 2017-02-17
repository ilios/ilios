<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * MeshQualifier API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class MeshQualifierTest extends AbstractEndpointTest
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
            'createdAt' => [[0], ['createdAt' => 'test']],
            'updatedAt' => [[0], ['updatedAt' => 'test']],
            'descriptors' => [[0], ['descriptors' => [1]]],
        ];
    }

}