<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadAamcResourceTypeData;
use App\Tests\Fixture\LoadTermData;
use App\Tests\ReadWriteEndpointTest;

/**
 * AamcResourceType API endpoint Test.
 * @group api_3
 */
class AamcResourceTypeTest extends ReadWriteEndpointTest
{
    protected string $testName =  'aamcResourceTypes';

    protected function getFixtures(): array
    {
        return [
            LoadAamcResourceTypeData::class,
            LoadTermData::class
        ];
    }

    /**
     * @inheritDoc
     */
    public static function putsToTest(): array
    {
        return [
            'title' => ['title', 'sure thing'],
            'description' => ['description', 'lorem ipsum'],
            'terms' => ['terms', [3]],
            'id' => ['id', 'FK1', $skipped = true],
        ];
    }

    /**
     * @inheritDoc
     */
    public static function readOnlyPropertiesToTest(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public static function filtersToTest(): array
    {
        return [
            'id' => [[2], ['id' => 'RE003']],
            'title' => [[0], ['title' => 'first title']],
            'description' => [[1], ['description' => 'second description']],
            'terms' => [[0], ['terms' => [1]]],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        return self::filtersToTest();
    }

    public function testPostTermAamcResourceType()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'aamcResourceType', 'terms');
    }

    public function testPutResourceTypeWithExtraData()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $data['garbage'] = 'LA Dodgers';

        $this->badPostTest($data);
    }

    public function testPutReadOnly($key = null, $id = null, $value = null, $skipped = false)
    {
        parent::markTestSkipped('Skipped');
    }
}
