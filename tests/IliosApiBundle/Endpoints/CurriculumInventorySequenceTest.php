<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * CurriculumInventorySequence API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class CurriculumInventorySequenceTest extends AbstractEndpointTest
{
    protected $testName =  'curriculuminventorysequence';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadCurriculumInventorySequenceData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'report' => ['report', $this->getFaker()->text],
            'description' => ['description', $this->getFaker()->text],
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
            'description' => [[0], ['description' => 'test']],
        ];
    }

}