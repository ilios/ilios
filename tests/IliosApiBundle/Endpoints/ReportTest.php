<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * Report API endpoint Test.
 * @group api_4
 */
class ReportTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'reports';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadReportData',
            'Tests\CoreBundle\Fixture\LoadUserData'
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text(25)],
            'school' => ['school', 3],
            'subject' => ['subject', $this->getFaker()->text(5)],
            'prepositionalObject' => ['prepositionalObject', $this->getFaker()->text(32)],
            'prepositionalObjectTableRowId' => ['prepositionalObjectTableRowId', '9'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest()
    {
        return [
            'id' => ['id', 1, 99],
            'createdAt' => ['createdAt', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[1, 2], ['id' => [2, 3]]],
            'title' => [[1], ['title' => 'second report']],
            'school' => [[2], ['school' => 1]],
            'subject' => [[2], ['subject' => 'subject3']],
            'prepositionalObject' => [[2], ['prepositionalObject' => 'object3']],
            'prepositionalObjectTableRowId' => [[1], ['prepositionalObjectTableRowId' => 14]],
            'user' => [[0, 1, 2], ['user' => 2]],
        ];
    }

    protected function getTimeStampFields()
    {
        return ['createdAt'];
    }
}
