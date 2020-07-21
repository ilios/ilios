<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\V1ReadEndpointTest;

/**
 * Competency API endpoint Test.
 * @group api_5
 */
class CompetencyV1Test extends V1ReadEndpointTest
{
    protected $testName =  'competencies';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadSchoolData',
            'App\Tests\Fixture\LoadTermData',
            'App\Tests\Fixture\LoadCompetencyData',
            'App\Tests\Fixture\LoadSessionData',
            'App\Tests\Fixture\LoadSessionTypeData',
            'App\Tests\Fixture\LoadCourseData',
            'App\Tests\Fixture\LoadAamcPcrsData',
            'App\Tests\Fixture\LoadProgramYearData',
            'App\Tests\Fixture\LoadSessionObjectiveData',
            'App\Tests\Fixture\LoadCourseObjectiveData',
            'App\Tests\Fixture\LoadProgramYearObjectiveData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function testGetOne()
    {
        $competencyData = $this->getDataLoader()->getOne();
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $v1Competency = $this->getOne($endpoint, $responseKey, $competencyData['id']);
        $v3Competency = $this->getOne($endpoint, $responseKey, $competencyData['id'], 'v3');
        $this->assertEquals($v3Competency['id'], $v1Competency['id']);
        $this->assertEquals($v3Competency['title'], $v1Competency['title']);
        $this->assertEquals($v3Competency['school'], $v1Competency['school']);
        $this->assertEquals($v3Competency['aamcPcrses'], $v1Competency['aamcPcrses']);
        $this->assertEquals($v3Competency['children'], $v1Competency['children']);
        $this->assertEquals($v3Competency['programYears'], $v1Competency['programYears']);
        $this->assertEquals($v3Competency['active'], $v1Competency['active']);
        $this->assertCount(count($v3Competency['programYearObjectives']), $v1Competency['objectives']);
    }
}
