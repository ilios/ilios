<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\V1ReadEndpointTest;

/**
 * Term API endpoint Test.
 * @group api_4
 */
class TermV1Test extends V1ReadEndpointTest
{
    protected $testName =  'terms';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadAamcResourceTypeData',
            'App\Tests\Fixture\LoadVocabularyData',
            'App\Tests\Fixture\LoadSchoolData',
            'App\Tests\Fixture\LoadCourseData',
            'App\Tests\Fixture\LoadProgramYearData',
            'App\Tests\Fixture\LoadSessionData',
            'App\Tests\Fixture\LoadOfferingData',
            'App\Tests\Fixture\LoadIlmSessionData',
            'App\Tests\Fixture\LoadLearningMaterialData',
            'App\Tests\Fixture\LoadCourseLearningMaterialData',
            'App\Tests\Fixture\LoadSessionLearningMaterialData',
            'App\Tests\Fixture\LoadMeshDescriptorData',
            'App\Tests\Fixture\LoadObjectiveData',
            'App\Tests\Fixture\LoadTermData',
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
        $termData = $this->getDataLoader()->getOne();
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $v1Term = $this->getOne($endpoint, $responseKey, $termData['id']);
        $v3Term = $this->getOne($endpoint, $responseKey, $termData['id'], 'v3');
        $this->assertEquals($v3Term['id'], $v1Term['id']);
        $this->assertEquals($v3Term['title'], $v1Term['title']);
        $this->assertEquals($v3Term['description'], $v1Term['description']);
        $this->assertEquals($v3Term['children'], $v1Term['children']);
        $this->assertEquals($v3Term['courses'], $v1Term['courses']);
        $this->assertEquals($v3Term['programYears'], $v1Term['programYears']);
        $this->assertEquals($v3Term['sessions'], $v1Term['sessions']);
        $this->assertEquals($v3Term['vocabulary'], $v1Term['vocabulary']);
        $this->assertEquals($v3Term['aamcResourceTypes'], $v1Term['aamcResourceTypes']);
        $this->assertEquals($v3Term['active'], $v1Term['active']);
        $this->assertArrayNotHasKey('sessionObjectives', $v1Term);
        $this->assertArrayNotHasKey('courseObjectives', $v1Term);
        $this->assertArrayNotHasKey('programYearObjectives', $v1Term);
    }
}
