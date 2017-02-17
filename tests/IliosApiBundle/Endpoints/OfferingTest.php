<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * Offering API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class OfferingTest extends AbstractTest
{
    protected $testName =  'offering';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadOfferingData',
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
            'room' => ['room', $this->getFaker()->text],
            'site' => ['site', $this->getFaker()->text],
            'startDate' => ['startDate', $this->getFaker()->text],
            'endDate' => ['endDate', $this->getFaker()->text],
            'session' => ['session', $this->getFaker()->text],
            'learnerGroups' => ['learnerGroups', [1]],
            'instructorGroups' => ['instructorGroups', [1]],
            'learners' => ['learners', [1]],
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
            'id' => [[0], ['filters[id]' => 1]],
            'room' => [[0], ['filters[room]' => 'test']],
            'site' => [[0], ['filters[site]' => 'test']],
            'startDate' => [[0], ['filters[startDate]' => 'test']],
            'endDate' => [[0], ['filters[endDate]' => 'test']],
            'updatedAt' => [[0], ['filters[updatedAt]' => 'test']],
            'session' => [[0], ['filters[session]' => 'test']],
            'learnerGroups' => [[0], ['filters[learnerGroups]' => [1]]],
            'instructorGroups' => [[0], ['filters[instructorGroups]' => [1]]],
            'learners' => [[0], ['filters[learners]' => [1]]],
            'instructors' => [[0], ['filters[instructors]' => [1]]],
        ];
    }

}