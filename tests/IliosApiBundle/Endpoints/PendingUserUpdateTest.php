<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * PendingUserUpdate API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class PendingUserUpdateTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'pendinguserupdates';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadPendingUserUpdateData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'type' => ['type', $this->getFaker()->text],
            'property' => ['property', $this->getFaker()->text],
            'value' => ['value', $this->getFaker()->text],
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
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 1]],
            'type' => [[0], ['type' => 'test']],
            'property' => [[0], ['property' => 'test']],
            'value' => [[0], ['value' => 'test']],
            'user' => [[0], ['user' => 'test']],
        ];
    }
}
