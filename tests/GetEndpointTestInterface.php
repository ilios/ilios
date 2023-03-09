<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Interface GetEndpointTestInterface
 * @package App\Tests
 */
interface GetEndpointTestInterface
{
    /**
     * @return array [[positions], [[filterKey, filterValue]]
     * the key for each item is reflected in the failure message
     * positions:  array of the positions the expected items from the DataLoader
     * filter: array containing the filterKey and filterValue we are testing
     */
    public static function filtersToTest(): array;

    /**
     * An array of GraphQL filters to test.
     */
    public static function graphQLFiltersToTest(): array;

    /**
     * Test fetching a single object
     */
    public function testGetOne();

    /**
     * Test fetching ALL objects
     */
    public function testGetAll();

    /**
     * Test that a bad ID produces a 404 response
     */
    public function testNotFound();

    /**
     * @param array $dataKeys
     * @param array $filterParts
     * @param bool $skipped
     */
    #[DataProvider('filtersToTest')]
    public function testFilters(array $dataKeys = [], array $filterParts = [], $skipped = false);

    #[DataProvider('graphQLFiltersToTest')]
    public function testGraphQLFilters(array $dataKeys = [], array $filterParts = [], $skipped = false);
}
