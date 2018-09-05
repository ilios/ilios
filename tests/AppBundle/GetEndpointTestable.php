<?php

namespace Tests\AppBundle;

/**
 * Trait GetEndpointTestable
 * @package Tests\AppBundle
 */
trait GetEndpointTestable
{
    /**
     * @see GetEndpointTestInterface::testGetOne()
     */
    public function testGetOne()
    {
        $this->getOneTest();
    }

    /**
     * @see GetEndpointTestInterface::testGetAll()
     */
    public function testGetAll()
    {
        $this->getAllTest();
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
        $expectedData = array_map(function ($i) use ($all) {
            return $all[$i];
        }, $dataKeys);
        $filters = [];
        foreach ($filterParts as $key => $value) {
            $filters["filters[{$key}]"] = $value;
        }
        $this->filterTest($filters, $expectedData);
    }
}
