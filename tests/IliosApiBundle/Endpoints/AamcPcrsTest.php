<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * AamcPcrs API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class AamcPcrsTest extends AbstractEndpointTest
{
    protected $testName =  'aamcpcrs';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadAamcPcrsData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'description' => ['description', $this->getFaker()->text],
            'competencies' => ['competencies', [1]],
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
            'description' => [[0], ['description' => 'test']],
            'competencies' => [[0], ['competencies' => [1]]],
        ];
    }

}