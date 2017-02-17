<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * LearnerGroup API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class LearnerGroupTest extends AbstractEndpointTest
{
    protected $testName =  'learnergroup';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadLearnerGroupData',
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
            'location' => ['location', $this->getFaker()->text],
            'cohort' => ['cohort', $this->getFaker()->text],
            'parent' => ['parent', $this->getFaker()->text],
            'children' => ['children', [1]],
            'ilmSessions' => ['ilmSessions', [1]],
            'offerings' => ['offerings', [1]],
            'instructorGroups' => ['instructorGroups', [1]],
            'users' => ['users', [1]],
            'instructors' => ['instructors', [1]],
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
            'title' => [[0], ['filters[title]' => 'test']],
            'location' => [[0], ['filters[location]' => 'test']],
            'cohort' => [[0], ['filters[cohort]' => 'test']],
            'parent' => [[0], ['filters[parent]' => 'test']],
            'children' => [[0], ['filters[children]' => [1]]],
            'ilmSessions' => [[0], ['filters[ilmSessions]' => [1]]],
            'offerings' => [[0], ['filters[offerings]' => [1]]],
            'instructorGroups' => [[0], ['filters[instructorGroups]' => [1]]],
            'users' => [[0], ['filters[users]' => [1]]],
            'instructors' => [[0], ['filters[instructors]' => [1]]],
        ];
    }

}