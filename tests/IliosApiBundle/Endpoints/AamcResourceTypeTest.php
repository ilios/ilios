<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * AamcResourceType API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class AamcResourceTypeTest extends AbstractEndpointTest
{
    protected $testName =  'aamcresourcetype';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadAamcResourceTypeData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text],
            'description' => ['description', $this->getFaker()->text],
            'terms' => ['terms', [1]],
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
            'id' => [[0], ['id' => 'test']],
            'title' => [[0], ['title' => 'test']],
            'description' => [[0], ['description' => 'test']],
            'terms' => [[0], ['terms' => [1]]],
        ];
    }

}