<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * AssessmentOption API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class AssessmentOptionTest extends AbstractTest
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
     *
     * returns an array of field / value pairs to modify
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
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
            'sessionTypes' => [[0], ['filters[sessionTypes]' => [1]]],
        ];
    }

}