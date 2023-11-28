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
    public function testPostOne(): void;

    /**
     * Test posting a single object with a service token
     */
    public function testPostOneWithServiceToken(): void;

    /**
     * Test a failure when posting an object
     */
    public function testPostBad(): void;

    /**
     * Test a failure when posting an object with a service token
     */
    public function testPostBadWithServiceToken(): void;

    /**
     * Test POST several of this type of object
     */
    public function testPostMany(): void;

    /**
     * Test POST several of this type of object with a service token
     */
    public function testPostManyWithServiceToken(): void;

    /**
     * Test posting a single object using JSON:API
     */
    public function testPostOneJsonApi(): void;

    /**
     * Test posting a single object using JSON:API with a service token
     */
    public function testPostOneJsonApiWithServiceToken(): void;

    /**
     * Test POST several of this type of object using JSON:API
     */
    public function testPostManyJsonApi(): void;

    /**
     * Test POST several of this type of object using JSON:API with a service token
     */
    public function testPostManyJsonApiWithServiceToken(): void;

    /**
     * Test a failure when posting an object anonymously.
     */
    public function testPostAnonymousAccessDenied(): void;
}
