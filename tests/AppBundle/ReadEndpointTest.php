<?php

namespace Tests\AppBundle;

/**
 * Class ReadEndpointTest
 * @package Tests\AppBundle
 */
abstract class ReadEndpointTest extends AbstractEndpointTest implements GetEndpointTestInterface
{
    use GetEndpointTestable;
}
