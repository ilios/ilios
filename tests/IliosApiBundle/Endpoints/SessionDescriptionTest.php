<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * SessionDescription API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class SessionDescriptionTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'sessiondescriptions';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadSessionDescriptionData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'session' => ['session', 1],
            'description' => ['description', $this->getFaker()->text],
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
            'session' => [[0], ['session' => 1]],
            'description' => [[0], ['description' => 'test']],
        ];
    }
}
