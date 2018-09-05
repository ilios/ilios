<?php

namespace Tests\AppBundle;

/**
 * Interface PostEndpointTestInterface
 * @package Tests\AppBundle
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
