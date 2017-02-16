<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * InstructorGroup API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class InstructorGroupTest extends AbstractTest
{
    protected $testName =  'instructorgroup';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadInstructorGroupData',
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
            'school' => ['school', $this->getFaker()->text],
            'learnerGroups' => ['learnerGroups', [1]],
            'ilmSessions' => ['ilmSessions', [1]],
            'users' => ['users', [1]],
            'offerings' => ['offerings', [1]],
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
            'school' => [[0], ['filters[school]' => 'test']],
            'learnerGroups' => [[0], ['filters[learnerGroups]' => [1]]],
            'ilmSessions' => [[0], ['filters[ilmSessions]' => [1]]],
            'users' => [[0], ['filters[users]' => [1]]],
            'offerings' => [[0], ['filters[offerings]' => [1]]],
        ];
    }

}