<?php

declare(strict_types=1);

namespace App\Tests;

use App\Tests\DataLoader\DataLoaderInterface;

trait QEndpointTrait
{
    abstract protected function qsToTest(): array;
    abstract protected function getDataLoader(): DataLoaderInterface;
    abstract protected function jsonApiFilterTest(array $filters, array $expectedData);
    abstract protected function filterTest(array $filters, array $expectedData, int $userId = 2);

    /**
     * @dataProvider qsToTest
     */
    public function testFindByQ(string $q, array $dataKeys): void
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData = array_map(fn($i) => $all[$i], $dataKeys);
        $filters = ['q' => $q];
        $this->filterTest($filters, $expectedData);
    }

    /**
     * @dataProvider qsToTest
     */
    public function testFindByQJsonApi(string $q, array $dataKeys): void
    {
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData = array_map(fn($i) => $all[$i], $dataKeys);
        $filters = ['q' => $q];
        $this->jsonApiFilterTest($filters, $expectedData);
    }
}
