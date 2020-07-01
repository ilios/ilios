<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\AbstractEndpointTest;

/**
 * Objective API endpoint Test.
 * @group api_5
 */
class ObjectiveTest extends AbstractEndpointTest
{
    protected $testName =  'objectives';

    protected $apiVersion = 'v1';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadCompetencyData',
            'App\Tests\Fixture\LoadCourseData',
            'App\Tests\Fixture\LoadProgramYearData',
            'App\Tests\Fixture\LoadSessionData',
            'App\Tests\Fixture\LoadMeshDescriptorData',
            'App\Tests\Fixture\LoadSessionObjectiveData',
            'App\Tests\Fixture\LoadCourseObjectiveData',
            'App\Tests\Fixture\LoadProgramYearObjectiveData',
        ];
    }
}
