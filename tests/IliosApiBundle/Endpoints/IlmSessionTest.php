<?php

namespace Tests\IliosApiBundle\Endpoints;

/**
 * IlmSession API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class IlmSessionTest extends AbstractTest
{
    protected $testName =  'ilmsession';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadIlmSessionData',
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
            'session' => ['session', 1],
            'hours' => ['hours', $this->getFaker()->text],
            'dueDate' => ['dueDate', $this->getFaker()->text],
            'learnerGroups' => ['learnerGroups', [1]],
            'instructorGroups' => ['instructorGroups', [1]],
            'instructors' => ['instructors', [1]],
            'learners' => ['learners', [1]],
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
            'session' => [[0], ['filters[session]' => 1]],
            'hours' => [[0], ['filters[hours]' => 'test']],
            'dueDate' => [[0], ['filters[dueDate]' => 'test']],
            'learnerGroups' => [[0], ['filters[learnerGroups]' => [1]]],
            'instructorGroups' => [[0], ['filters[instructorGroups]' => [1]]],
            'instructors' => [[0], ['filters[instructors]' => [1]]],
            'learners' => [[0], ['filters[learners]' => [1]]],
        ];
    }

}