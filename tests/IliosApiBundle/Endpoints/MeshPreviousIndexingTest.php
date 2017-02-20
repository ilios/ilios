<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * MeshPreviousIndexing API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class MeshPreviousIndexingTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'meshpreviousindexings';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadMeshPreviousIndexingData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'descriptor' => ['descriptor', $this->getFaker()->text],
            'previousIndexing' => ['previousIndexing', $this->getFaker()->text],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnliesToTest()
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
            'descriptor' => [[0], ['descriptor' => 'test']],
            'previousIndexing' => [[0], ['previousIndexing' => 'test']],
        ];
    }

}