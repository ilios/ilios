<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * ProgramYear API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class ProgramYearTest extends AbstractTest
{
    protected $testName =  'programyear';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadProgramYearData',
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
            'startYear' => ['startYear', $this->getFaker()->text],
            'locked' => ['locked', false],
            'archived' => ['archived', false],
            'publishedAsTbd' => ['publishedAsTbd', false],
            'published' => ['published', false],
            'program' => ['program', $this->getFaker()->text],
            'cohort' => ['cohort', $this->getFaker()->text],
            'directors' => ['directors', [1]],
            'competencies' => ['competencies', [1]],
            'terms' => ['terms', [1]],
            'objectives' => ['objectives', [1]],
            'stewards' => ['stewards', [1]],
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
            'startYear' => [[0], ['filters[startYear]' => 'test']],
            'locked' => [[0], ['filters[locked]' => false]],
            'archived' => [[0], ['filters[archived]' => false]],
            'publishedAsTbd' => [[0], ['filters[publishedAsTbd]' => false]],
            'published' => [[0], ['filters[published]' => false]],
            'program' => [[0], ['filters[program]' => 'test']],
            'cohort' => [[0], ['filters[cohort]' => 'test']],
            'directors' => [[0], ['filters[directors]' => [1]]],
            'competencies' => [[0], ['filters[competencies]' => [1]]],
            'terms' => [[0], ['filters[terms]' => [1]]],
            'objectives' => [[0], ['filters[objectives]' => [1]]],
            'stewards' => [[0], ['filters[stewards]' => [1]]],
        ];
    }

}