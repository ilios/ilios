<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Fixture\LoadAamcResourceTypeData;
use App\Tests\Fixture\LoadTermData;

/**
 * AamcResourceType API endpoint Test.
 * @group api_3
 */
class AamcResourceTypeTest extends AbstractReadWriteEndpoint
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
    public function putsToTest(): array
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
    public function readOnlyPropertiesToTest(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest(): array
    {
        return [
            'id' => [[2], ['id' => 'RE003']],
            'title' => [[0], ['title' => 'first title']],
            'description' => [[1], ['description' => 'second description']],
            'terms' => [[0], ['terms' => [1]]],
        ];
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
}
