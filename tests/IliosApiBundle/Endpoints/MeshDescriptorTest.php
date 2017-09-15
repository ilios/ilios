<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * MeshDescriptor API endpoint Test.
 * @group api_3
 */
class MeshDescriptorTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'meshDescriptors';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadMeshDescriptorData',
            'Tests\CoreBundle\Fixture\LoadCourseData',
            'Tests\CoreBundle\Fixture\LoadObjectiveData',
            'Tests\CoreBundle\Fixture\LoadSessionData',
            'Tests\CoreBundle\Fixture\LoadMeshConceptData',
            'Tests\CoreBundle\Fixture\LoadMeshTreeData',
            'Tests\CoreBundle\Fixture\LoadMeshPreviousIndexingData',
            'Tests\CoreBundle\Fixture\LoadMeshQualifierData',
            'Tests\CoreBundle\Fixture\LoadMeshTermData',
            'Tests\CoreBundle\Fixture\LoadSessionLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadCourseLearningMaterialData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'name' => ['name', $this->getFaker()->text],
            'annotation' => ['annotation', $this->getFaker()->text],
            'courses' => ['courses', [2]],
            'objectives' => ['objectives', [1]],
            'sessions' => ['sessions', [2]],
            'concepts' => ['concepts', [1]],
            'qualifiers' => ['qualifiers', [1]],
            'trees' => ['trees', [1], $skipped = true],
            'sessionLearningMaterials' => ['sessionLearningMaterials', [1, 2]],
            'courseLearningMaterials' => ['courseLearningMaterials', [1]],
            'previousIndexing' => ['previousIndexing', 2, $skipped = true],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest()
    {
        return [
            'createdAt' => ['createdAt', 'abc1', 99],
            'updatedAt' => ['updatedAt', 'abc1', 99],
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

    public function testPostMeshDescriptorCourse()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'meshDescriptors', 'courses');
    }

    public function testPostMeshDescriptorSession()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'meshDescriptors', 'sessions');
    }

    public function testPostMeshDescriptorObjective()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'meshDescriptors', 'objectives');
    }

    public function testPostMeshDescriptorConcepts()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'descriptors', 'meshConcepts', 'concepts');
    }

    public function testPostMeshDescriptorQualifier()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'descriptors', 'meshQualifiers', 'qualifiers');
    }

    public function testPostMeshDescriptorSessionLearningMaterial()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'meshDescriptors', 'sessionLearningMaterials');
    }

    public function testPostMeshDescriptorCourseLearningMaterial()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'meshDescriptors', 'courseLearningMaterials');
    }
}
