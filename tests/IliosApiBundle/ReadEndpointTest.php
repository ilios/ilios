<?php

namespace Tests\IliosApiBundle;

/**
 * Class ReadEndpointTest
 * @package Tests\IliosApiBundle
 */
abstract class ReadEndpointTest extends AbstractEndpointTest
{
    /**
     * @return array [[positions], [[filterKey, filterValue]]
     * the key for each item is reflected in the failure message
     * positions:  array of the positions the expected items from the DataLoader
     * filter: array containing the filterKey and filterValue we are testing
     */
    public abstract function filtersToTest();

    /**
     * Test fetching a single object
     */
    public function testGetOne()
    {
        $this->getOneTest();
    }

    /**
     * Test fetching ALL objects
     */
    public function testGetAll()
    {
        $this->getAllTest();
    }

    /**
     * Test that a bad ID produces a 404 response
     */
    public function testNotFound()
    {
        $this->notFoundTest(99);
    }

    /**
     * @param array $dataKeys
     * @param array $filterParts
     * @param bool $skipped
     *
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
