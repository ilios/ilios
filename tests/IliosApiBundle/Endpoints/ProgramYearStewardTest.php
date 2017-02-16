<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * ProgramYearSteward API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class ProgramYearStewardTest extends AbstractTest
{
    protected $testName =  'programyearsteward';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadProgramYearStewardData',
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
            'department' => ['department', $this->getFaker()->text],
            'programYear' => ['programYear', $this->getFaker()->text],
            'school' => ['school', $this->getFaker()->text],
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
            'department' => [[0], ['filters[department]' => 'test']],
            'programYear' => [[0], ['filters[programYear]' => 'test']],
            'school' => [[0], ['filters[school]' => 'test']],
        ];
    }

}