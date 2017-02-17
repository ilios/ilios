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
     *
     * returns an array of field / value pairs to modify
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
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
     *
     * returns an array of field / value pairs that are readOnly
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
     */
    public function readOnliesToTest()
    {
        return [
            'id' => ['id', 1, 99],
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
            'id' => [[0], ['filters[id]' => 1]],
            'notes' => [[0], ['filters[notes]' => 'test']],
            'required' => [[0], ['filters[required]' => false]],
            'publicNotes' => [[0], ['filters[publicNotes]' => false]],
            'course' => [[0], ['filters[course]' => 'test']],
            'learningMaterial' => [[0], ['filters[learningMaterial]' => 'test']],
            'meshDescriptors' => [[0], ['filters[meshDescriptors]' => [1]]],
            'position' => [[0], ['filters[position]' => 1]],
        ];
    }

}