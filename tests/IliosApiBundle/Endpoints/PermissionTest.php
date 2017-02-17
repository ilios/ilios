<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * Permission API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class PermissionTest extends AbstractEndpointTest
{
    protected $testName =  'permission';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadPermissionData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'user' => ['user', $this->getFaker()->text],
            'canRead' => ['canRead', false],
            'canWrite' => ['canWrite', false],
            'tableRowId' => ['tableRowId', $this->getFaker()->randomDigit],
            'tableName' => ['tableName', $this->getFaker()->text],
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
            'user' => [[0], ['user' => 'test']],
            'canRead' => [[0], ['canRead' => false]],
            'canWrite' => [[0], ['canWrite' => false]],
            'tableRowId' => [[0], ['tableRowId' => 1]],
            'tableName' => [[0], ['tableName' => 'test']],
        ];
    }

}