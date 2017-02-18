<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * Alert API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class AlertTest extends AbstractEndpointTest
{
    protected $testName =  'alerts';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadAlertData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'tableRowId' => ['tableRowId', $this->getFaker()->randomDigit],
            'tableName' => ['tableName', $this->getFaker()->text],
            'additionalText' => ['additionalText', $this->getFaker()->text],
            'dispatched' => ['dispatched', false],
            'changeTypes' => ['changeTypes', [1]],
            'instigators' => ['instigators', [1]],
            'recipients' => ['recipients', [1]],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnliesToTest()
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 1]],
            'tableRowId' => [[0], ['tableRowId' => 1]],
            'tableName' => [[0], ['tableName' => 'test']],
            'additionalText' => [[0], ['additionalText' => 'test']],
            'dispatched' => [[0], ['dispatched' => false]],
            'changeTypes' => [[0], ['changeTypes' => [1]]],
            'instigators' => [[0], ['instigators' => [1]]],
            'recipients' => [[0], ['recipients' => [1]]],
        ];
    }

}