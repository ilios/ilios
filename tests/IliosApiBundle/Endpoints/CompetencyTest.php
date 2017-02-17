<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * Competency API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class CompetencyTest extends AbstractEndpointTest
{
    protected $testName =  'competency';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadCompetencyData',
        ];
    }

    /**
     * @inheritDoc
     *
     * returns an array of field / value pairs to modify
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text],
            'school' => ['school', $this->getFaker()->text],
            'objectives' => ['objectives', [1]],
            'parent' => ['parent', $this->getFaker()->text],
            'children' => ['children', [1]],
            'aamcPcrses' => ['aamcPcrses', [1]],
            'programYears' => ['programYears', [1]],
            'active' => ['active', false],
        ];
    }

    /**
     * @inheritDoc
     *
     * returns an array of field / value pairs that are readOnly
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
     */
    public function readOnliesToTest()
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     *
     * returns an array of filters to test
     * the key for each item is reflected in the failure message
     * the first item is an array of the positions the expected items
     * can be found in the data loader
     * the second item is the filter we are testing
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['filters[id]' => 1]],
            'title' => [[0], ['filters[title]' => 'test']],
            'school' => [[0], ['filters[school]' => 'test']],
            'objectives' => [[0], ['filters[objectives]' => [1]]],
            'parent' => [[0], ['filters[parent]' => 'test']],
            'children' => [[0], ['filters[children]' => [1]]],
            'aamcPcrses' => [[0], ['filters[aamcPcrses]' => [1]]],
            'programYears' => [[0], ['filters[programYears]' => [1]]],
            'active' => [[0], ['filters[active]' => false]],
        ];
    }

}