<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * CurriculumInventoryReport API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class CurriculumInventoryReportTest extends AbstractTest
{
    protected $testName =  'curriculuminventoryreport';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryReportData',
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
            'name' => ['name', $this->getFaker()->text],
            'description' => ['description', $this->getFaker()->text],
            'year' => ['year', $this->getFaker()->randomDigit],
            'startDate' => ['startDate', $this->getFaker()->text],
            'endDate' => ['endDate', $this->getFaker()->text],
            'export' => ['export', $this->getFaker()->text],
            'sequence' => ['sequence', $this->getFaker()->text],
            'sequenceBlocks' => ['sequenceBlocks', [1]],
            'program' => ['program', $this->getFaker()->text],
            'academicLevels' => ['academicLevels', [1]],
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
            'name' => [[0], ['filters[name]' => 'test']],
            'description' => [[0], ['filters[description]' => 'test']],
            'year' => [[0], ['filters[year]' => 1]],
            'startDate' => [[0], ['filters[startDate]' => 'test']],
            'endDate' => [[0], ['filters[endDate]' => 'test']],
            'export' => [[0], ['filters[export]' => 'test']],
            'sequence' => [[0], ['filters[sequence]' => 'test']],
            'sequenceBlocks' => [[0], ['filters[sequenceBlocks]' => [1]]],
            'program' => [[0], ['filters[program]' => 'test']],
            'academicLevels' => [[0], ['filters[academicLevels]' => [1]]],
        ];
    }

}