<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * AssessmentOption API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class AssessmentOptionTest extends AbstractEndpointTest
{
    protected $testName =  'assessmentoption';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadAssessmentOptionData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'name' => ['name', $this->getFaker()->text],
            'sessionTypes' => ['sessionTypes', [1]],
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
            'name' => [[0], ['name' => 'test']],
            'sessionTypes' => [[0], ['sessionTypes' => [1]]],
        ];
    }

}