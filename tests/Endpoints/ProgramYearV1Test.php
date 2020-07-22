<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\V1ReadEndpointTest;

/**
 * ProgramYear API v1 endpoint Test.
 * @group api_3
 */
class ProgramYearV1Test extends V1ReadEndpointTest
{
    protected $testName = 'programYears';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadProgramYearData',
            'App\Tests\Fixture\LoadProgramData',
            'App\Tests\Fixture\LoadCohortData',
            'App\Tests\Fixture\LoadUserData',
            'App\Tests\Fixture\LoadCompetencyData',
            'App\Tests\Fixture\LoadTermData',
            'App\Tests\Fixture\LoadProgramYearStewardData',
            'App\Tests\Fixture\LoadSessionData',
            'App\Tests\Fixture\LoadCourseData',
            'App\Tests\Fixture\LoadProgramYearObjectiveData',
            'App\Tests\Fixture\LoadCourseObjectiveData',
            'App\Tests\Fixture\LoadSessionObjectiveData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function testGetOne()
    {
        $programYearData = $this->getDataLoader()->getOne();
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $v1ProgramYear = $this->getOne($endpoint, $responseKey, $programYearData['id']);
        $v3ProgramYear = $this->getOne($endpoint, $responseKey, $programYearData['id'], 'v3');
        $programYearObjective = $this->getOne(
            'programyearobjectives',
            'programYearObjectives',
            $v3ProgramYear['programYearObjectives'][0],
            'v3'
        );
        $objective = $this->getFiltered(
            'objectives',
            'objectives',
            ['filters[programYearObjectives]' => $programYearObjective['id']]
        )[0];
        $this->assertEquals($v3ProgramYear['id'], $v1ProgramYear['id']);
        $this->assertEquals($v3ProgramYear['startYear'], $v1ProgramYear['startYear']);
        $this->assertEquals($v3ProgramYear['locked'], $v1ProgramYear['locked']);
        $this->assertEquals($v3ProgramYear['archived'], $v1ProgramYear['archived']);
        $this->assertEquals($v3ProgramYear['program'], $v1ProgramYear['program']);
        $this->assertEquals($v3ProgramYear['cohort'], $v1ProgramYear['cohort']);
        $this->assertEquals($v3ProgramYear['directors'], $v1ProgramYear['directors']);
        $this->assertEquals($v3ProgramYear['competencies'], $v1ProgramYear['competencies']);
        $this->assertEquals($v3ProgramYear['terms'], $v1ProgramYear['terms']);
        $this->assertEquals(count($v3ProgramYear['programYearObjectives']), count($v1ProgramYear['objectives']));
        $this->assertEquals($objective['id'], $v1ProgramYear['objectives'][0]);
        $this->assertEquals($programYearData['stewards'], $v1ProgramYear['stewards']);
        $this->assertEquals($programYearData['published'], $v1ProgramYear['published']);
        $this->assertEquals($programYearData['publishedAsTbd'], $v1ProgramYear['publishedAsTbd']);
    }
}
