<?php

declare(strict_types=1);

namespace App\Tests;

use function property_exists;

/**
 * Trait GetEndpointTestable
 * @package App\Tests
 */
trait GetEndpointTestable
{
    /**
     * @see GetEndpointTestInterface::testGetOne()
     */
    public function testGetOne()
    {
        $this->getOneTest();
        $this->getOneJsonApiTest();
    }

    /**
     * @see GetEndpointTestInterface::testGetAll()
     */
    public function testGetAll()
    {
        $this->getAllTest();
        $this->getAllWithLimitAndOffsetTest();
        $this->getAllJsonApiTest();
        $this->getAllWithLimitAndOffsetJsonApiTest();
        if (property_exists($this, 'isGraphQLTestable') && $this->isGraphQLTestable) {
            $this->getAllGraphQLTest();
        }
    }

    /**
     * @see GetEndpointTestInterface::testNotFound()
     */
    public function testNotFound()
    {
        $this->notFoundTest(99);
    }

    /**
     * @see GetEndpointTestInterface::testFilters()
     * @dataProvider filtersToTest
     */
    public function testFilters(array $dataKeys = [], array $filterParts = [], $skipped = false)
    {
        if ($skipped) {
            $this->markTestSkipped();
        }
        if (empty($filterParts)) {
            $this->markTestSkipped('Missing filters tests for this endpoint');
            return;
        }
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData = array_map(fn($i) => $all[$i], $dataKeys);
        $filters = [];
        foreach ($filterParts as $key => $value) {
            $filters["filters[{$key}]"] = $value;
        }
        $this->filterTest($filters, $expectedData);
    }
}
