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
            'App\Tests\Fixture\LoadObjectiveData',
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
        $v2ProgramYear = $this->getOne($endpoint, $responseKey, $programYearData['id'], 'v2');
        $programYearObjective = $this->getOne(
            'programyearobjectives',
            'programYearObjectives',
            $v2ProgramYear['programYearObjectives'][0],
            'v2'
        );
        $this->assertEquals($v2ProgramYear['id'], $v1ProgramYear['id']);
        $this->assertEquals($v2ProgramYear['startYear'], $v1ProgramYear['startYear']);
        $this->assertEquals($v2ProgramYear['locked'], $v1ProgramYear['locked']);
        $this->assertEquals($v2ProgramYear['archived'], $v1ProgramYear['archived']);
        $this->assertEquals($v2ProgramYear['publishedAsTbd'], $v1ProgramYear['publishedAsTbd']);
        $this->assertEquals($v2ProgramYear['published'], $v1ProgramYear['published']);
        $this->assertEquals($v2ProgramYear['program'], $v1ProgramYear['program']);
        $this->assertEquals($v2ProgramYear['cohort'], $v1ProgramYear['cohort']);
        $this->assertEquals($v2ProgramYear['directors'], $v1ProgramYear['directors']);
        $this->assertEquals($v2ProgramYear['competencies'], $v1ProgramYear['competencies']);
        $this->assertEquals($v2ProgramYear['terms'], $v1ProgramYear['terms']);
        $this->assertEquals($v2ProgramYear['stewards'], $v1ProgramYear['stewards']);
        $this->assertEquals(count($v2ProgramYear['programYearObjectives']), count($v1ProgramYear['objectives']));
        $this->assertEquals($programYearObjective['objective'], $v1ProgramYear['objectives'][0]);
    }
}
