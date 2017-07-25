<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * CourseLearningMaterial API endpoint Test.
 * @group api_1
 */
class CourseLearningMaterialTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'courseLearningMaterials';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadCourseLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadMeshDescriptorData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'notes' => ['notes', $this->getFaker()->text],
            'required' => ['required', false],
            'publicNotes' => ['publicNotes', false],
            'course' => ['course', 4],
            'learningMaterial' => ['learningMaterial', 2],
            'meshDescriptors' => ['meshDescriptors', ['abc3']],
            'position' => ['position', $this->getFaker()->randomDigit],
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
            'notes' => [[2], ['notes' => 'third note']],
            'notRequired' => [[1], ['required' => false]],
            'required' => [[0, 2, 3], ['required' => true]],
            'notPublicNotes' => [[2], ['publicNotes' => false]],
            'publicNotes' => [[0, 1, 3], ['publicNotes' => true]],
            'course' => [[2], ['course' => 4]],
            'learningMaterial' => [[1], ['learningMaterial' => 2]],
            'meshDescriptors' => [[0, 2], ['meshDescriptors' => ['abc1']], $skipped = true],
            'position' => [[1], ['position' => 1]],
        ];
    }
}
