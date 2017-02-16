<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * CurriculumInventorySequenceBlock API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class CurriculumInventorySequenceBlockTest extends AbstractTest
{
    protected $testName =  'curriculuminventorysequenceblock';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadCurriculumInventorySequenceBlockData',
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
            'description' => ['description', $this->getFaker()->text],
            'required' => ['required', $this->getFaker()->randomDigit],
            'childSequenceOrder' => ['childSequenceOrder', $this->getFaker()->randomDigit],
            'orderInSequence' => ['orderInSequence', $this->getFaker()->randomDigit],
            'minimum' => ['minimum', $this->getFaker()->randomDigit],
            'maximum' => ['maximum', $this->getFaker()->randomDigit],
            'track' => ['track', false],
            'startDate' => ['startDate', $this->getFaker()->text],
            'endDate' => ['endDate', $this->getFaker()->text],
            'duration' => ['duration', $this->getFaker()->randomDigit],
            'academicLevel' => ['academicLevel', $this->getFaker()->text],
            'course' => ['course', $this->getFaker()->text],
            'parent' => ['parent', $this->getFaker()->text],
            'children' => ['children', [1]],
            'report' => ['report', $this->getFaker()->text],
            'sessions' => ['sessions', [1]],
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
            'description' => [[0], ['filters[description]' => 'test']],
            'required' => [[0], ['filters[required]' => 1]],
            'childSequenceOrder' => [[0], ['filters[childSequenceOrder]' => 1]],
            'orderInSequence' => [[0], ['filters[orderInSequence]' => 1]],
            'minimum' => [[0], ['filters[minimum]' => 1]],
            'maximum' => [[0], ['filters[maximum]' => 1]],
            'track' => [[0], ['filters[track]' => false]],
            'startDate' => [[0], ['filters[startDate]' => 'test']],
            'endDate' => [[0], ['filters[endDate]' => 'test']],
            'duration' => [[0], ['filters[duration]' => 1]],
            'academicLevel' => [[0], ['filters[academicLevel]' => 'test']],
            'course' => [[0], ['filters[course]' => 'test']],
            'parent' => [[0], ['filters[parent]' => 'test']],
            'children' => [[0], ['filters[children]' => [1]]],
            'report' => [[0], ['filters[report]' => 'test']],
            'sessions' => [[0], ['filters[sessions]' => [1]]],
        ];
    }

}