<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * Alert API endpoint Test.
 * @group api_5
 */
class AlertTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'alerts';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadAlertData',
            'Tests\CoreBundle\Fixture\LoadAlertChangeTypeData',
            'Tests\CoreBundle\Fixture\LoadUserData',
            'Tests\CoreBundle\Fixture\LoadSchoolData'
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'tableRowId' => ['tableRowId', $this->getFaker()->randomDigit],
            'tableName' => ['tableName', $this->getFaker()->text(20)],
            'additionalText' => ['additionalText', $this->getFaker()->text(100)],
            'dispatched' => ['dispatched', false],
            'changeTypes' => ['changeTypes', [2, 3]],
            'instigators' => ['instigators', [1]],
            'recipients' => ['recipients', [1]],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest()
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
            'ids' => [[1, 2], ['id' => [2, 3]]],
            'tableRowId' => [[0, 2], ['tableRowId' => 1]],
            'tableName' => [[2], ['tableName' => 'session']],
            'additionalText' => [[1], ['additionalText' => 'second text']],
            'dispatched' => [[2], ['dispatched' => false]],
            'changeTypes' => [[0, 1], ['changeTypes' => [1]]],
            'instigators' => [[0, 1], ['instigators' => [2]]],
            'recipients' => [[0, 2], ['recipients' => [2]]],
        ];
    }
}
