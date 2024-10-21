<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

/**
 * Base class for all of our standardized API endpoint tests that read data from the API.
 */
abstract class AbstractReadEndpoint extends AbstractEndpoint implements GetEndpointTestInterface
{
    /**
     * @var bool Set this to FALSE if the test should not attempt to read data from GraphQL endpoint.
     */
    protected bool $isGraphQLTestable = true;

    protected bool $enableGetTestsWithServiceToken = true;

    abstract public static function filtersToTest(): array;

    abstract public static function graphQLFiltersToTest(): array;

    public function testGetOne(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $this->runGetOneTest($jwt);
    }

    public function testGetOneWithServiceToken(): void
    {
        if (!$this->enableGetTestsWithServiceToken) {
            $this->markTestSkipped('Get one test with service token skipped for this endpoint.');
        }
        $jwt = $this->createJwtForEnabledServiceToken($this->kernelBrowser);
        $this->runGetOneTest($jwt);
    }

    public function testGetAll(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $this->runGetAllTest($jwt);
    }

    public function testGetAllWithServiceToken(): void
    {
        if (!$this->enableGetTestsWithServiceToken) {
            $this->markTestSkipped('Get all test with service token skipped for this endpoint.');
        }
        $jwt = $this->createJwtForEnabledServiceToken($this->kernelBrowser);
        $this->runGetAllTest($jwt);
    }

    public function testGraphQL(): void
    {
        if (!property_exists($this, 'isGraphQLTestable') || !$this->isGraphQLTestable) {
            $this->markTestSkipped();
        }
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $this->getAllGraphQLTest($jwt);
        $this->getSomeGraphQLTest($jwt);
    }

    public function testNotFound(): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $this->runNotFoundTest($jwt);
    }

    public function testNotFoundWithServiceToken(): void
    {
        if (!$this->enableGetTestsWithServiceToken) {
            $this->markTestSkipped('Not found test with service token skipped for this endpoint.');
        }
        $jwt = $this->createJwtForEnabledServiceToken($this->kernelBrowser);
        $this->runNotFoundTest($jwt);
    }

    /**
     * @dataProvider filtersToTest
     */
    public function testFilters(array $dataKeys = [], array $filterParts = []): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $this->runFiltersTest($jwt, $dataKeys, $filterParts);
    }

    /**
     * @dataProvider filtersToTest
     */
    public function testFiltersWithServiceToken(array $dataKeys = [], array $filterParts = []): void
    {
        if (!$this->enableGetTestsWithServiceToken) {
            $this->markTestSkipped('Filters test with service token skipped for this endpoint.');
        }
        $jwt = $this->createJwtForEnabledServiceToken($this->kernelBrowser);
        $this->runFiltersTest($jwt, $dataKeys, $filterParts);
    }

    /**
     * @dataProvider graphQLFiltersToTest
     */
    public function testGraphQLFilters(array $dataKeys = [], array $filterParts = [], bool $skipped = false): void
    {
        if (!property_exists($this, 'isGraphQLTestable') || !$this->isGraphQLTestable) {
            $this->markTestSkipped();
        }
        if ($skipped) {
            $this->markTestSkipped();
        }
        if (empty($filterParts)) {
            $this->markTestSkipped('Missing filters tests for this endpoint');
        }
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $idField = $dataLoader->getIdField();
        $expectedIds = array_map(fn($i) => (string) $all[$i][$idField], $dataKeys);
        $this->graphQLFilterTest($filterParts, $expectedIds, $jwt);
    }

    public function testAnonymousAccessDenied(): void
    {
        $this->anonymousAccessDeniedOneTest();
        $this->anonymousAccessDeniedAllTest();
    }

    protected function runGetOneTest(string $jwt): void
    {
        $this->getOneTest($jwt);
        $this->getOneJsonApiTest($jwt);
    }

    protected function runGetAllTest(string $jwt): void
    {
        $this->getAllTest($jwt);
        $this->getAllWithLimitAndOffsetTest($jwt);
        $this->getAllJsonApiTest($jwt);
        $this->getAllWithLimitAndOffsetJsonApiTest($jwt);
    }

    protected function runNotFoundTest(string $jwt): void
    {
        $this->notFoundTest(99, $jwt);
    }

    protected function runFiltersTest(string $jwt, array $dataKeys = [], array $filterParts = []): void
    {
        if (empty($filterParts)) {
            $this->markTestSkipped('Missing filters tests for this endpoint');
        }
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData = array_map(fn($i) => $all[$i], $dataKeys);
        $filters = [];
        foreach ($filterParts as $key => $value) {
            $filters["filters[$key]"] = $value;
        }
        $this->filterTest($filters, $expectedData, $jwt);
    }
}
