<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\ReadEndpointTest;

/**
 * LearningMaterialStatus API endpoint Test.
 * @group api_3
 */
class LearningMaterialStatusTest extends ReadEndpointTest
{
    protected $testName =  'learningMaterialStatuses';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadLearningMaterialStatusData',
            'Tests\CoreBundle\Fixture\LoadLearningMaterialData'
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[1, 2], ['id' => [2, 3]]],
            'title' => [[1], ['title' => 'Final']],
        ];
    }
}
