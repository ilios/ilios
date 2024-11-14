<?php

declare(strict_types=1);

namespace App\Tests;

use App\Tests\DataLoader\DataLoaderInterface;
use Exception;

trait QEndpointTrait
{
    abstract public static function qsToTest(): array;
    abstract protected function getDataLoader(): DataLoaderInterface;
    abstract protected function jsonApiFilterTest(array $filters, array $expectedData, string $jwt): void;
    abstract protected function filterTest(array $filters, array $expectedData, string $jwt): void;

    abstract public function testFindByQWithLimit(): void;
    abstract public function testFindByQWithOffset(): void;
    abstract public function testFindByQWithOffsetAndLimit(): void;
    abstract public function testFindByQWithOffsetAndLimitJsonApi(): void;

    /**
     * @throws Exception
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('qsToTest')]
    public function testFindByQ(string $q, array $dataKeys): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData = array_map(fn($i) => $all[$i], $dataKeys);
        $filters = ['q' => $q];
        $this->filterTest($filters, $expectedData, $jwt);
    }

    /**
     * @throws Exception
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('qsToTest')]
    public function testFindByQJsonApi(string $q, array $dataKeys): void
    {
        $jwt = $this->createJwtForRootUser($this->kernelBrowser);
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData = array_map(fn($i) => $all[$i], $dataKeys);
        $filters = ['q' => $q];
        $this->jsonApiFilterTest($filters, $expectedData, $jwt);
    }
}
