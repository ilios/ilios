<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * CurriculumInventoryAcademicLevel API endpoint Test.
 * @group api_4
 */
class CurriculumInventoryAcademicLevelTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'curriculumInventoryAcademicLevels';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryAcademicLevelData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryReportData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryExportData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventorySequenceBlockData',
            'Tests\CoreBundle\Fixture\LoadProgramData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'name' => ['name', $this->getFaker()->text(50)],
            'description' => ['description', $this->getFaker()->text],
            'level' => ['level', $this->getFaker()->randomDigit],
            'sequenceBlocks' => ['sequenceBlocks', [2], $skipped = true],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest()
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
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'name' => [[1], ['name' => 'second name']],
            'description' => [[0], ['description' => 'first description']],
            'level' => [[1], ['level' => 2]],
            'report' => [[0, 1], ['report' => '1']],
            'sequenceBlocks' => [[1], ['sequenceBlocks' => [2]], $skipped = true],
        ];
    }
}
