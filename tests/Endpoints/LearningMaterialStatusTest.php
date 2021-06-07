<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\ReadEndpointTest;

/**
 * LearningMaterialStatus API endpoint Test.
 * @group api_3
 */
class LearningMaterialStatusTest extends ReadEndpointTest
{
    protected string $testName =  'learningMaterialStatuses';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadLearningMaterialStatusData',
            'App\Tests\Fixture\LoadLearningMaterialData'
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
