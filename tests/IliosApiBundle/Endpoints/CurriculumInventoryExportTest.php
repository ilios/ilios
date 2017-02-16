<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * CurriculumInventoryExport API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class CurriculumInventoryExportTest extends AbstractTest
{
    protected $testName =  'curriculuminventoryexport';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryExportData',
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
            'report' => ['report', $this->getFaker()->text],
            'createdBy' => ['createdBy', $this->getFaker()->text],
            'createdAt' => ['createdAt', $this->getFaker()->text],
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
            'report' => [[0], ['filters[report]' => 'test']],
            'createdBy' => [[0], ['filters[createdBy]' => 'test']],
            'createdAt' => [[0], ['filters[createdAt]' => 'test']],
        ];
    }

}