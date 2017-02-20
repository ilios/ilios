<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * Report API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
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
        ];
    }

    /**
     * @inheritDoc
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
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 1]],
            'title' => [[0], ['title' => 'test']],
            'createdAt' => [[0], ['createdAt' => 'test']],
            'school' => [[0], ['school' => 'test']],
            'subject' => [[0], ['subject' => 'test']],
            'prepositionalObject' => [[0], ['prepositionalObject' => 'test']],
            'prepositionalObjectTableRowId' => [[0], ['prepositionalObjectTableRowId' => 'test']],
            'user' => [[0], ['user' => 'test']],
        ];
    }

}