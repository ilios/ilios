<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * MeshDescriptor API endpoint Test.
 * @group api_3
 */
class MeshDescriptorTest extends AbstractMeshTest
{
    protected $testName =  'meshDescriptors';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\AppBundle\Fixture\LoadMeshDescriptorData',
            'Tests\AppBundle\Fixture\LoadCourseData',
            'Tests\AppBundle\Fixture\LoadObjectiveData',
            'Tests\AppBundle\Fixture\LoadSessionData',
            'Tests\AppBundle\Fixture\LoadMeshConceptData',
            'Tests\AppBundle\Fixture\LoadMeshTreeData',
            'Tests\AppBundle\Fixture\LoadMeshPreviousIndexingData',
            'Tests\AppBundle\Fixture\LoadMeshQualifierData',
            'Tests\AppBundle\Fixture\LoadMeshTermData',
            'Tests\AppBundle\Fixture\LoadSessionLearningMaterialData',
            'Tests\AppBundle\Fixture\LoadCourseLearningMaterialData',
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
            'objectives' => [[0], ['objectives' => [1]], $skipped = true],
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
}
