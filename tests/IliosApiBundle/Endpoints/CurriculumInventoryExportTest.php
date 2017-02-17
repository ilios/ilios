<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * CurriculumInventoryExport API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class CurriculumInventoryExportTest extends AbstractEndpointTest
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
            'report' => [[0], ['report' => 'test']],
            'createdBy' => [[0], ['createdBy' => 'test']],
            'createdAt' => [[0], ['createdAt' => 'test']],
        ];
    }

}