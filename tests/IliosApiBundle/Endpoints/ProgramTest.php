<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * Program API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class ProgramTest extends AbstractTest
{
    protected $testName =  'program';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadProgramData',
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
            'shortTitle' => ['shortTitle', $this->getFaker()->text],
            'duration' => ['duration', $this->getFaker()->randomDigit],
            'publishedAsTbd' => ['publishedAsTbd', false],
            'published' => ['published', false],
            'school' => ['school', $this->getFaker()->text],
            'programYears' => ['programYears', [1]],
            'curriculumInventoryReports' => ['curriculumInventoryReports', [1]],
            'directors' => ['directors', [1]],
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
            'shortTitle' => [[0], ['filters[shortTitle]' => 'test']],
            'duration' => [[0], ['filters[duration]' => 1]],
            'publishedAsTbd' => [[0], ['filters[publishedAsTbd]' => false]],
            'published' => [[0], ['filters[published]' => false]],
            'school' => [[0], ['filters[school]' => 'test']],
            'programYears' => [[0], ['filters[programYears]' => [1]]],
            'curriculumInventoryReports' => [[0], ['filters[curriculumInventoryReports]' => [1]]],
            'directors' => [[0], ['filters[directors]' => [1]]],
        ];
    }

}