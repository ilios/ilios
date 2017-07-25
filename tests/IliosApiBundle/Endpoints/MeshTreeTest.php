<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * MeshTree API endpoint Test.
 * @group api_4
 */
class MeshTreeTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'meshTrees';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadMeshTreeData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'treeNumber' => ['treeNumber', $this->getFaker()->text(31)],
            'descriptor' => ['descriptor', 'abc2'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest()
    {
        return [
            'id' => ['id', 1, 99],
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
            'treeNumber' => [[1], ['treeNumber' => 'tree2']],
            'descriptor' => [[0, 1], ['descriptor' => 'abc1']],
        ];
    }
}
