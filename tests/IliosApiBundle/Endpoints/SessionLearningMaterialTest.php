<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * SessionLearningMaterial API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class SessionLearningMaterialTest extends AbstractTest
{
    protected $testName =  'sessionlearningmaterial';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadSessionLearningMaterialData',
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
            'session' => ['session', 1],
            'learningMaterial' => ['learningMaterial', 1],
            'meshDescriptors' => ['meshDescriptors', [1]],
            'position' => ['position', $this->getFaker()->randomDigit],
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
            'session' => [[0], ['filters[session]' => 1]],
            'learningMaterial' => [[0], ['filters[learningMaterial]' => 1]],
            'meshDescriptors' => [[0], ['filters[meshDescriptors]' => [1]]],
            'position' => [[0], ['filters[position]' => 1]],
        ];
    }

}