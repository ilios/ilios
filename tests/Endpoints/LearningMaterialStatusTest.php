<?php

namespace Tests\App\Endpoints;

use Tests\App\ReadEndpointTest;

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
            'Tests\AppBundle\Fixture\LoadLearningMaterialStatusData',
            'Tests\AppBundle\Fixture\LoadLearningMaterialData'
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
