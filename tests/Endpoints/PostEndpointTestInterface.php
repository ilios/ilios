<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

/**
 * Interface PostEndpointTestInterface
 * @package App\Tests
 */
interface PostEndpointTestInterface
{
    /**
     * Test posting a single object
     */
    public function testPostOne();

    /**
     * Test a failure when posting an object
     */
    public function testPostBad();

    /**
     * Test POST several of this type of object
     */
    public function testPostMany();
}
