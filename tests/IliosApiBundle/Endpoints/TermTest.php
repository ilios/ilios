<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * Term API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class TermTest extends AbstractTest
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
     *
     * returns an array of field / value pairs to modify
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
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
            'courses' => [[0], ['filters[courses]' => [1]]],
            'description' => [[0], ['filters[description]' => 'test']],
            'parent' => [[0], ['filters[parent]' => 'test']],
            'children' => [[0], ['filters[children]' => [1]]],
            'programYears' => [[0], ['filters[programYears]' => [1]]],
            'sessions' => [[0], ['filters[sessions]' => [1]]],
            'title' => [[0], ['filters[title]' => 'test']],
            'vocabulary' => [[0], ['filters[vocabulary]' => 'test']],
            'aamcResourceTypes' => [[0], ['filters[aamcResourceTypes]' => [1]]],
            'active' => [[0], ['filters[active]' => false]],
        ];
    }

}