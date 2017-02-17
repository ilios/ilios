<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * Term API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class TermTest extends AbstractEndpointTest
{
    protected $testName =  'term';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadTermData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'courses' => ['courses', [1]],
            'description' => ['description', $this->getFaker()->text],
            'parent' => ['parent', $this->getFaker()->text],
            'children' => ['children', [1]],
            'programYears' => ['programYears', [1]],
            'sessions' => ['sessions', [1]],
            'title' => ['title', $this->getFaker()->text],
            'vocabulary' => ['vocabulary', $this->getFaker()->text],
            'aamcResourceTypes' => ['aamcResourceTypes', [1]],
            'active' => ['active', false],
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
            'courses' => [[0], ['courses' => [1]]],
            'description' => [[0], ['description' => 'test']],
            'parent' => [[0], ['parent' => 'test']],
            'children' => [[0], ['children' => [1]]],
            'programYears' => [[0], ['programYears' => [1]]],
            'sessions' => [[0], ['sessions' => [1]]],
            'title' => [[0], ['title' => 'test']],
            'vocabulary' => [[0], ['vocabulary' => 'test']],
            'aamcResourceTypes' => [[0], ['aamcResourceTypes' => [1]]],
            'active' => [[0], ['active' => false]],
        ];
    }

}