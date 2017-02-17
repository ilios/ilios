<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * CourseLearningMaterial API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class CourseLearningMaterialTest extends AbstractEndpointTest
{
    protected $testName =  'courselearningmaterial';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadCourseLearningMaterialData',
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
            'course' => ['course', $this->getFaker()->text],
            'learningMaterial' => ['learningMaterial', $this->getFaker()->text],
            'meshDescriptors' => ['meshDescriptors', [1]],
            'position' => ['position', $this->getFaker()->randomDigit],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnliesToTest()
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
            'notes' => [[0], ['notes' => 'test']],
            'required' => [[0], ['required' => false]],
            'publicNotes' => [[0], ['publicNotes' => false]],
            'course' => [[0], ['course' => 'test']],
            'learningMaterial' => [[0], ['learningMaterial' => 'test']],
            'meshDescriptors' => [[0], ['meshDescriptors' => [1]]],
            'position' => [[0], ['position' => 1]],
        ];
    }

}