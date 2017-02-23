<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * CurriculumInventoryReport API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class CurriculumInventoryReportTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'curriculumInventoryReports';

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
     */
    public function putsToTest()
    {
        return [
            'name' => ['name', $this->getFaker()->text],
            'description' => ['description', $this->getFaker()->text],
            'year' => ['year', $this->getFaker()->randomDigit],
            'startDate' => ['startDate', $this->getFaker()->iso8601],
            'endDate' => ['endDate', $this->getFaker()->iso8601],
            'export' => ['export', $this->getFaker()->text],
            'sequence' => ['sequence', $this->getFaker()->text],
            'sequenceBlocks' => ['sequenceBlocks', [1]],
            'program' => ['program', $this->getFaker()->text],
            'academicLevels' => ['academicLevels', [1]],
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
            'name' => [[0], ['name' => 'test']],
            'description' => [[0], ['description' => 'test']],
            'year' => [[0], ['year' => 1]],
            'startDate' => [[0], ['startDate' => 'test']],
            'endDate' => [[0], ['endDate' => 'test']],
            'export' => [[0], ['export' => 'test']],
            'sequence' => [[0], ['sequence' => 'test']],
            'sequenceBlocks' => [[0], ['sequenceBlocks' => [1]]],
            'program' => [[0], ['program' => 'test']],
            'academicLevels' => [[0], ['academicLevels' => [1]]],
        ];
    }
}
