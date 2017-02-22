<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * SessionType API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class SessionTypeTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'sessiontypes';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadSessionTypeData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text],
            'assessmentOption' => ['assessmentOption', $this->getFaker()->text],
            'school' => ['school', $this->getFaker()->text],
            'aamcMethods' => ['aamcMethods', [1]],
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
            'title' => [[0], ['title' => 'test']],
            'assessmentOption' => [[0], ['assessmentOption' => 'test']],
            'school' => [[0], ['school' => 'test']],
            'aamcMethods' => [[0], ['aamcMethods' => [1]]],
        ];
    }
}
