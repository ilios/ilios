<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * CurriculumInventoryAcademicLevel API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class CurriculumInventoryAcademicLevelTest extends AbstractTest
{
    protected $testName =  'curriculuminventoryacademiclevel';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryAcademicLevelData',
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
            'level' => ['level', $this->getFaker()->randomDigit],
            'report' => ['report', $this->getFaker()->text],
            'sequenceBlocks' => ['sequenceBlocks', [1]],
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
            'level' => [[0], ['filters[level]' => 1]],
            'report' => [[0], ['filters[report]' => 'test']],
            'sequenceBlocks' => [[0], ['filters[sequenceBlocks]' => [1]]],
        ];
    }

}