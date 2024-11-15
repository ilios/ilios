<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Interface PutEndpointTestInterface
 * @package App\Tests
 */
interface PutEndpointTestInterface
{
    /**
     * @return array [field, value]
     * field / value pairs to modify
     * field: readonly property name on the entity
     * value: something to set it to
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
     */
    public static function putsToTest(): array;

    /**
     * @return array [field, value, id]
     *
     * field / value / id sets that are readOnly
     * field: readonly property name on the entity
     * value: something to set it to
     * id: the ID of the object we want to test.  The has to be provided separately
     * because we can't extract it from the $data without invalidating this test
     *
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
     */
    public static function readOnlyPropertiesToTest(): array;

    #[DataProvider('putsToTest')]
    public function testPut(string $key, mixed $value): void;

    #[DataProvider('putsToTest')]
    public function testPutWithServiceToken(string $key, mixed $value): void;

    /**
     * Test PUTing each test data item to ensure
     * they all are saved as we would expect
     */
    public function testPutForAllData(): void;

    /**
     * Test PUTing each test data item to ensure
     * they all are saved as we would expect, using a service token.
     */
    public function testPutForAllDataWithServiceToken(): void;

    #[DataProvider('readOnlyPropertiesToTest')]
    public function testPutReadOnly(
        ?string $key = null,
        mixed $id = null,
        mixed $value = null,
        bool $skipped = false
    ): void;

    #[DataProvider('readOnlyPropertiesToTest')]
    public function testPutReadOnlyWithServiceToken(
        ?string $key = null,
        mixed $id = null,
        mixed $value = null,
        bool $skipped = false
    ): void;

    /**
     * Test to ensure that an anonymous PUT request is denied.
     */
    public function testPutAnonymousAccessDenied(): void;
}
