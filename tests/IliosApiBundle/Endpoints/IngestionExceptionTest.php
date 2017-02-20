<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * IngestionException API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class IngestionExceptionTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'ingestionexceptions';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadIngestionExceptionData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'uid' => ['uid', $this->getFaker()->text],
            'user' => ['user', $this->getFaker()->text],
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
            'uid' => [[0], ['uid' => 'test']],
            'user' => [[0], ['user' => 'test']],
        ];
    }

}