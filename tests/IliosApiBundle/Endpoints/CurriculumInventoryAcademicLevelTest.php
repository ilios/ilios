<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * CurriculumInventoryAcademicLevel API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class CurriculumInventoryAcademicLevelTest extends AbstractEndpointTest
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
            'level' => [[0], ['level' => 1]],
            'report' => [[0], ['report' => 'test']],
            'sequenceBlocks' => [[0], ['sequenceBlocks' => [1]]],
        ];
    }

}