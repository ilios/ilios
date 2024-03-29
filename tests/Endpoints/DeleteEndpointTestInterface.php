<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

/**
 * Interface DeleteEndpointTestInterface
 * @package App\Tests
 */
interface DeleteEndpointTestInterface
{
    /**
     * Test deleting data
     */
    public function testDelete(): void;

    /**
     * Test deleting data with a service token
     */
    public function testDeleteWithServiceToken(): void;
}
