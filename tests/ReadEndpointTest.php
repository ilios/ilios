<?php

namespace Tests\App;

/**
 * Class ReadEndpointTest
 * @package Tests\App
 */
abstract class ReadEndpointTest extends AbstractEndpointTest implements GetEndpointTestInterface
{
    use GetEndpointTestable;
}
