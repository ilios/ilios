<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * Report API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class ReportTest extends AbstractEndpointTest
{
    protected $testName =  'report';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadReportData',
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
            'subject' => ['subject', $this->getFaker()->text],
            'prepositionalObject' => ['prepositionalObject', $this->getFaker()->text],
            'prepositionalObjectTableRowId' => ['prepositionalObjectTableRowId', $this->getFaker()->text],
            'user' => ['user', $this->getFaker()->text],
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
            'createdAt' => ['createdAt', 1, 99],
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
            'createdAt' => [[0], ['filters[createdAt]' => 'test']],
            'school' => [[0], ['filters[school]' => 'test']],
            'subject' => [[0], ['filters[subject]' => 'test']],
            'prepositionalObject' => [[0], ['filters[prepositionalObject]' => 'test']],
            'prepositionalObjectTableRowId' => [[0], ['filters[prepositionalObjectTableRowId]' => 'test']],
            'user' => [[0], ['filters[user]' => 'test']],
        ];
    }

}