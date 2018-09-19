<?php

namespace Tests\App\Endpoints;

use Tests\App\ReadEndpointTest;

/**
 * CurriculumInventoryAcademicLevel API endpoint Test.
 * @group api_4
 */
class CurriculumInventoryAcademicLevelTest extends ReadEndpointTest
{
    protected $testName =  'curriculumInventoryAcademicLevels';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\AppBundle\Fixture\LoadCurriculumInventoryAcademicLevelData',
            'Tests\AppBundle\Fixture\LoadCurriculumInventoryReportData',
            'Tests\AppBundle\Fixture\LoadCurriculumInventoryExportData',
            'Tests\AppBundle\Fixture\LoadCurriculumInventorySequenceBlockData',
            'Tests\AppBundle\Fixture\LoadProgramData',
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
