<?php

namespace Tests\App\Endpoints;

use Tests\App\ReadEndpointTest;

/**
 * LearningMaterialUserRole API endpoint Test.
 * @group api_1
 */
class LearningMaterialUserRoleTest extends ReadEndpointTest
{
    protected $testName =  'learningMaterialUserRoles';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\AppBundle\Fixture\LoadLearningMaterialUserRoleData',
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
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'title' => [[1], ['title' => 'second lm user role']],
        ];
    }
}
