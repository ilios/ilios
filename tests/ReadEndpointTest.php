<?php

namespace Tests\App;

/**
 * Class ReadEndpointTest
 * @package Tests\AppBundle
 */
abstract class ReadEndpointTest extends AbstractEndpointTest implements GetEndpointTestInterface
{
    use GetEndpointTestable;
}
