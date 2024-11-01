<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

interface PatchEndpointTestInterface
{
    /**
     * Test PATCH to update a single object using JSON:API
     */
    public function testPatchJsonApi(string $key, mixed $value): void;

    /**
     * Test PATCH to update a single object using JSON:API with a service token
     */
    public function testPatchJsonApiWithServiceToken(string $key, mixed $value): void;

    /**
     * Test PATCHing each test data item to ensure
     * they all are saved as we would expect, using JSON:API
     */
    public function testPatchForAllDataJsonApi(): void;

    /**
     * Test PATCHing each test data item to ensure
     * they all are saved as we would expect, using JSON:API and a service token
     */
    public function testPatchForAllDataJsonApiWithServiceToken(): void;

    /**
     * Test a failure when PATCHing an object anonymously.
     */
    public function testPatchAnonymousAccessDenied(): void;
}
