<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

/**
 * MeshDescriptor API endpoint Test.
 * @group api_3
 * @group time-sensitive
 */
class MeshDescriptorTest extends AbstractMeshTest
{
    protected string $testName =  'meshDescriptors';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'App\Tests\Fixture\LoadMeshDescriptorData',
            'App\Tests\Fixture\LoadCourseData',
            'App\Tests\Fixture\LoadSessionData',
            'App\Tests\Fixture\LoadMeshConceptData',
            'App\Tests\Fixture\LoadMeshTreeData',
            'App\Tests\Fixture\LoadMeshPreviousIndexingData',
            'App\Tests\Fixture\LoadMeshQualifierData',
            'App\Tests\Fixture\LoadMeshTermData',
            'App\Tests\Fixture\LoadSessionLearningMaterialData',
            'App\Tests\Fixture\LoadCourseLearningMaterialData',
            'App\Tests\Fixture\LoadCourseObjectiveData',
            'App\Tests\Fixture\LoadSessionObjectiveData',
            'App\Tests\Fixture\LoadProgramYearObjectiveData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 'abc1']],
            'ids' => [[1, 2], ['id' => ['abc2', 'abc3']]],
            'name' => [[2], ['name' => 'desc3']],
            'annotation' => [[0], ['annotation' => 'annotation1']],
            'courses' => [[0, 1], ['courses' => [2, 3]]],
            'sessions' => [[1], ['sessions' => [3]]],
            'concepts' => [[0], ['concepts' => [1]], $skipped = true],
            'qualifiers' => [[0], ['qualifiers' => [1]], $skipped = true],
            'trees' => [[0], ['trees' => [1]], $skipped = true],
            'sessionLearningMaterials' => [[0], ['sessionLearningMaterials' => [1]], $skipped = true],
            'courseLearningMaterials' => [[0], ['courseLearningMaterials' => [1]], $skipped = true],
            'learningMaterials' => [[0], ['learningMaterials' => [1, 2]]],
            'previousIndexing' => [[1], ['previousIndexing' => 2], $skipped = true],
            'terms' => [[0, 1, 2], ['terms' => [1, 2, 3]]],
            'sessionTypes' => [[0, 1, 2], ['sessionTypes' => [2]]],
        ];
    }

    protected function getTimeStampFields()
    {
        return ['updatedAt', 'createdAt'];
    }

    public function qsToTest()
    {
        return [
            ['abc1', [0]],
            ['desc2', [1]],
            ['desc', [0, 1, 2]],
            ['annotation2', [1]],
            ['second previous indexing', [1]],
            ['second term', [0]],
            ['second concept', [0]],
            ['first scopeNote', [0]],
            ['first casn', [0]],
        ];
    }

    /**
     * @dataProvider qsToTest
     * @param $q
     * @param $dataKeys
     */
    public function testFindByQ($q, $dataKeys)
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData = array_map(function ($i) use ($all) {
            return $all[$i];
        }, $dataKeys);
        $filters = ['q' => $q];
        $this->filterTest($filters, $expectedData);
    }

    /**
     * @dataProvider qsToTest
     */
    public function testFindByQJsonApi(string $q, array $dataKeys)
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData = array_map(function ($i) use ($all) {
            return $all[$i];
        }, $dataKeys);
        $filters = ['q' => $q];
        $this->jsonApiFilterTest($filters, $expectedData);
    }

    /**
     * Ensure offset and limit work
     */
    public function testFindByQWithLimit()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => $all[0]['name'], 'limit' => 1];
        $this->filterTest($filters, [$all[0]]);
        $filters = ['q' => 'desc', 'limit' => 2];
        $this->filterTest($filters, [$all[0], $all[1]]);
    }

    /**
     * Ensure offset and limit work
     */
    public function testFindByQWithOffset()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => $all[0]['name'], 'offset' => 0];
        $this->filterTest($filters, [$all[0]]);
    }

    /**
     * Ensure offset and limit work
     */
    public function testFindByQWithOffsetAndLimit()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => $all[0]['name'], 'offset' => 0, 'limit' => 1];
        $this->filterTest($filters, [$all[0]]);
        $filters = ['q' => 'desc', 'offset' => 1, 'limit' => 1];
        $this->filterTest($filters, [$all[1]]);
    }

    public function testFindByQWithOffsetAndLimitJsonApi()
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $filters = ['q' => $all[0]['name'], 'offset' => 0, 'limit' => 1];
        $this->filterTest($filters, [$all[0]]);
        $filters = ['q' => 'desc', 'offset' => 1, 'limit' => 1];
        $this->jsonApiFilterTest($filters, [$all[1]]);
    }
}
