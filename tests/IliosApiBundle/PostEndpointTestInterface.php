<?php

namespace Tests\IliosApiBundle;

/**
 * Interface PostEndpointTestInterface
 * @package Tests\IliosApiBundle
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
