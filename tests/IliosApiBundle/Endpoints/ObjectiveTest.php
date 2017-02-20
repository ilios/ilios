<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * Objective API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class ObjectiveTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'objectives';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadObjectiveData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text],
            'competency' => ['competency', $this->getFaker()->text],
            'courses' => ['courses', [1]],
            'programYears' => ['programYears', [1]],
            'sessions' => ['sessions', [1]],
            'parents' => ['parents', [1]],
            'children' => ['children', [1]],
            'meshDescriptors' => ['meshDescriptors', [1]],
            'ancestor' => ['ancestor', $this->getFaker()->text],
            'descendants' => ['descendants', [1]],
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
            'competency' => [[0], ['competency' => 'test']],
            'courses' => [[0], ['courses' => [1]]],
            'programYears' => [[0], ['programYears' => [1]]],
            'sessions' => [[0], ['sessions' => [1]]],
            'parents' => [[0], ['parents' => [1]]],
            'children' => [[0], ['children' => [1]]],
            'meshDescriptors' => [[0], ['meshDescriptors' => [1]]],
            'ancestor' => [[0], ['ancestor' => 'test']],
            'descendants' => [[0], ['descendants' => [1]]],
        ];
    }

}