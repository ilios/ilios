<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * MeshDescriptor API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class MeshDescriptorTest extends AbstractEndpointTest
{
    protected $testName =  'meshdescriptors';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadMeshDescriptorData',
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
            'courses' => ['courses', [1]],
            'objectives' => ['objectives', [1]],
            'sessions' => ['sessions', [1]],
            'concepts' => ['concepts', [1]],
            'qualifiers' => ['qualifiers', [1]],
            'trees' => ['trees', [1]],
            'sessionLearningMaterials' => ['sessionLearningMaterials', [1]],
            'courseLearningMaterials' => ['courseLearningMaterials', [1]],
            'previousIndexing' => ['previousIndexing', $this->getFaker()->text],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnliesToTest()
    {
        return [
            'id' => ['id', 1, 99],
            'createdAt' => ['createdAt', 1, 99],
            'updatedAt' => ['updatedAt', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 'test']],
            'name' => [[0], ['name' => 'test']],
            'annotation' => [[0], ['annotation' => 'test']],
            'createdAt' => [[0], ['createdAt' => 'test']],
            'updatedAt' => [[0], ['updatedAt' => 'test']],
            'courses' => [[0], ['courses' => [1]]],
            'objectives' => [[0], ['objectives' => [1]]],
            'sessions' => [[0], ['sessions' => [1]]],
            'concepts' => [[0], ['concepts' => [1]]],
            'qualifiers' => [[0], ['qualifiers' => [1]]],
            'trees' => [[0], ['trees' => [1]]],
            'sessionLearningMaterials' => [[0], ['sessionLearningMaterials' => [1]]],
            'courseLearningMaterials' => [[0], ['courseLearningMaterials' => [1]]],
            'previousIndexing' => [[0], ['previousIndexing' => 'test']],
        ];
    }

}