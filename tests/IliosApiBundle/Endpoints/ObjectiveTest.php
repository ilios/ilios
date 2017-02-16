<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * Objective API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class ObjectiveTest extends AbstractTest
{
    protected $testName =  'objective';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadObjectiveData',
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
            'title' => ['title', $this->getFaker()->text],
            'competency' => ['competency', $this->getFaker()->text],
            'courses' => ['courses', [1]],
            'programYears' => ['programYears', [1]],
            'sessions' => ['sessions', [1]],
            'parents' => ['parents', [1]],
            'children' => ['children', [1]],
            'meshDescriptors' => ['meshDescriptors', [1]],
            'ancestor' => ['ancestor', $this->getFaker()->text],
            'descendants' => ['descendants', [1]],
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
            'title' => [[0], ['filters[title]' => 'test']],
            'competency' => [[0], ['filters[competency]' => 'test']],
            'courses' => [[0], ['filters[courses]' => [1]]],
            'programYears' => [[0], ['filters[programYears]' => [1]]],
            'sessions' => [[0], ['filters[sessions]' => [1]]],
            'parents' => [[0], ['filters[parents]' => [1]]],
            'children' => [[0], ['filters[children]' => [1]]],
            'meshDescriptors' => [[0], ['filters[meshDescriptors]' => [1]]],
            'ancestor' => [[0], ['filters[ancestor]' => 'test']],
            'descendants' => [[0], ['filters[descendants]' => [1]]],
        ];
    }

}