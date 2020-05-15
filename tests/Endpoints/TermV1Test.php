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
        $v2Term = $this->getOne($endpoint, $responseKey, $termData['id'], 'v2');
        $this->assertEquals($v2Term['id'], $v1Term['id']);
        $this->assertEquals($v2Term['title'], $v1Term['title']);
        $this->assertEquals($v2Term['description'], $v1Term['description']);
        $this->assertEquals($v2Term['children'], $v1Term['children']);
        $this->assertEquals($v2Term['courses'], $v1Term['courses']);
        $this->assertEquals($v2Term['programYears'], $v1Term['programYears']);
        $this->assertEquals($v2Term['sessions'], $v1Term['sessions']);
        $this->assertEquals($v2Term['vocabulary'], $v1Term['vocabulary']);
        $this->assertEquals($v2Term['aamcResourceTypes'], $v1Term['aamcResourceTypes']);
        $this->assertEquals($v2Term['active'], $v1Term['active']);
        $this->assertArrayNotHasKey('sessionObjectives', $v1Term);
        $this->assertArrayNotHasKey('courseObjectives', $v1Term);
        $this->assertArrayNotHasKey('programYearObjectives', $v1Term);
    }
}
