<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\ReadWriteEndpointTest;

/**
 * Term API endpoint Test.
 * @group api_4
 */
class TermTest extends ReadWriteEndpointTest
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
            'App\Tests\Fixture\LoadTermData',
            'App\Tests\Fixture\LoadSessionObjectiveData',
            'App\Tests\Fixture\LoadCourseObjectiveData',
            'App\Tests\Fixture\LoadProgramYearObjectiveData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'description' => ['description', $this->getFaker()->text(200)],
            'descriptionNull' => ['description', null],
            'title' => ['title', $this->getFaker()->text(100)],
            'courses' => ['courses', [1]],
            'parent' => ['parent', 2],
            'children' => ['children', [1], $skipped = true],
            'programYears' => ['programYears', [1]],
            'sessions' => ['sessions', [1]],
            'vocabulary' => ['vocabulary', 2],
            'aamcResourceTypes' => ['aamcResourceTypes', [1], $skipped = true],
            'active' => ['active', false],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest()
    {
        return [
            'id' => ['id', 1, 99],
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
            'title' => [[1], ['title' => 'second term']],
            'courses' => [[0, 1, 3, 4], ['courses' => [1]]],
            'description' => [[2], ['description' => 'third description']],
            'parent' => [[1, 2], ['parent' => 1]],
            'children' => [[0], ['children' => [3]], $skipped = true],
            'programYears' => [[0, 3], ['programYears' => [2]]],
            'sessions' => [[0, 1, 3, 4], ['sessions' => [1, 2]]],
            'vocabulary' => [[3, 4, 5], ['vocabulary' => 2]],
            'aamcResourceTypes' => [[1, 2], ['aamcResourceTypes' => ['RE002']]],
            'active' => [[0, 1, 3, 4], ['active' => true]],
            'notActive' => [[2, 5], ['active' => false]],
            'sessionTypes' => [[0, 1, 2, 3, 4, 5], ['sessionTypes' => [1, 2]]],
            'instructors' => [[0, 1, 3, 4], ['instructors' => [2]]],
            'instructorGroups' => [[0, 1, 3, 4], ['instructorGroups' => [1, 2, 3]]],
            'learningMaterials' => [[0, 1, 2, 4, 5], ['learningMaterials' => [1, 2, 3]]],
            'competencies' => [[0, 1, 4], ['competencies' => [1, 2]]],
            'meshDescriptors' => [[0, 1, 2, 3, 4, 5], ['meshDescriptors' => ['abc1', 'abc2', 'abc3']]],
            'programs' => [[0, 3], ['programs' => [1]]],
            'schools' => [[0, 1, 2], ['schools' => [1]]],
            'courseObjectives' => [[0, 3], ['courseObjectives' => [1]]],
            'sessionObjectives' => [[2, 3], ['sessionObjectives' => [1]]],
            'programYearObjectives' => [[1, 3], ['programYearObjectives' => [1]]],

        ];
    }

    public function testCreateTopLevelTerm()
    {
        $dataLoader = $this->getDataLoader();

        $postData = $dataLoader->create();
        $postData['parent'] = null;
        $this->postOne(
            'terms',
            'term',
            'terms',
            $postData
        );
    }

    public function testCannotCreateTermWithEmptyTitle()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $data['title'] = '';
        $this->badPostTest($data);
    }

    public function testCannotCreateTermWithNoTitle()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        unset($data['title']);
        $this->badPostTest($data);
    }

    public function testCannotSaveTermWithEmptyTitle()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $data['title'] = '';
        $this->badPutTest($data, $data['id']);
    }

    public function testCannotSaveTermWithNoTitle()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        unset($data['title']);
        $this->badPutTest($data, $data['id']);
    }
}
