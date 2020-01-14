<?php

declare(strict_types=1);

namespace App\Tests;

/**
 * Class ReadEndpointTest
 * @package App\Tests
 */
abstract class ReadEndpointTest extends AbstractEndpointTest implements GetEndpointTestInterface
{
    use GetEndpointTestable;
}
