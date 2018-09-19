<?php

namespace Tests\App\Endpoints;

use Tests\App\ReadWriteEndpointTest;

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
            'Tests\App\Fixture\LoadAamcResourceTypeData',
            'Tests\App\Fixture\LoadVocabularyData',
            'Tests\App\Fixture\LoadSchoolData',
            'Tests\App\Fixture\LoadCourseData',
            'Tests\App\Fixture\LoadProgramYearData',
            'Tests\App\Fixture\LoadSessionData',
            'Tests\App\Fixture\LoadOfferingData',
            'Tests\App\Fixture\LoadIlmSessionData',
            'Tests\App\Fixture\LoadLearningMaterialData',
            'Tests\App\Fixture\LoadCourseLearningMaterialData',
            'Tests\App\Fixture\LoadSessionLearningMaterialData',
            'Tests\App\Fixture\LoadMeshDescriptorData',
            'Tests\App\Fixture\LoadObjectiveData',
            'Tests\App\Fixture\LoadTermData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'description' => ['description', $this->getFaker()->text(200)],
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
            'competencies' => [[0, 1, 2, 3, 4, 5], ['competencies' => [1, 2]]],
            'meshDescriptors' => [[0, 1, 2, 3, 4, 5], ['meshDescriptors' => ['abc1', 'abc2', 'abc3']]],
            'programs' => [[0, 3], ['programs' => [1]]],
            'schools' => [[0, 1, 2], ['schools' => [1]]],
        ];
    }
}
