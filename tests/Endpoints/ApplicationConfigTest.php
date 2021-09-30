<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadApplicationConfigData;
use App\Tests\ReadWriteEndpointTest;

/**
 * ApplicationConfig API endpoint Test.
 * @group api_3
 */
class ApplicationConfigTest extends ReadWriteEndpointTest
{
    protected string $testName =  'applicationConfigs';
    protected bool $isGraphQLTestable = false;

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            LoadApplicationConfigData::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'value' => ['value', $this->getFaker()->text()],
            'name' => ['name', $this->getFaker()->text(100)],
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
            'name' => [[1], ['name' => 'second name']],
            'value' => [[2], ['value' => 'third value']],
        ];
    }
}
