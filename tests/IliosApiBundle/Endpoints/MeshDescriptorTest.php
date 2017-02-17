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
    protected $testName =  'meshdescriptor';

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
     *
     * returns an array of field / value pairs to modify
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
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
     *
     * returns an array of field / value pairs that are readOnly
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
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
     *
     * returns an array of filters to test
     * the key for each item is reflected in the failure message
     * the first item is an array of the positions the expected items
     * can be found in the data loader
     * the second item is the filter we are testing
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['filters[id]' => 'test']],
            'name' => [[0], ['filters[name]' => 'test']],
            'annotation' => [[0], ['filters[annotation]' => 'test']],
            'createdAt' => [[0], ['filters[createdAt]' => 'test']],
            'updatedAt' => [[0], ['filters[updatedAt]' => 'test']],
            'courses' => [[0], ['filters[courses]' => [1]]],
            'objectives' => [[0], ['filters[objectives]' => [1]]],
            'sessions' => [[0], ['filters[sessions]' => [1]]],
            'concepts' => [[0], ['filters[concepts]' => [1]]],
            'qualifiers' => [[0], ['filters[qualifiers]' => [1]]],
            'trees' => [[0], ['filters[trees]' => [1]]],
            'sessionLearningMaterials' => [[0], ['filters[sessionLearningMaterials]' => [1]]],
            'courseLearningMaterials' => [[0], ['filters[courseLearningMaterials]' => [1]]],
            'previousIndexing' => [[0], ['filters[previousIndexing]' => 'test']],
        ];
    }

}