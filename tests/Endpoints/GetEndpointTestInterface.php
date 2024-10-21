<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

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
    public function testGetOne(): void;

    /**
     * Test fetching a single object with a service token
     */
    public function testGetOneWithServiceToken(): void;

    /**
     * Test fetching ALL objects
     */
    public function testGetAll(): void;

    /**
     * Test fetching ALL objects with a service token
     */
    public function testGetAllWithServiceToken(): void;

    /**
     * Test that a bad ID produces a 404 response
     */
    public function testNotFound(): void;

    /**
     * Test that a bad ID produces a 404 response
     */
    public function testNotFoundWithServiceToken(): void;

    /**
     *
     * @dataProvider filtersToTest
     */
    public function testFilters(array $dataKeys = [], array $filterParts = []): void;

    /**
     *
     * @dataProvider filtersToTest
     */
    public function testFiltersWithServiceToken(array $dataKeys = [], array $filterParts = []): void;

    /**
     * Tests reading data from the GraphQL search endpoint.
     */
    public function testGraphQL(): void;

    /**
     * Tests reading data from the GraphQL search endpoint while providing input for filtering.
     * @dataProvider graphQLFiltersToTest
     */
    public function testGraphQLFilters(array $dataKeys = [], array $filterParts = [], bool $skipped = false): void;

    /**
     * Tests Access Denied conditions on the given API endpoint for anonymous user access.
     */
    public function testAnonymousAccessDenied(): void;
}
