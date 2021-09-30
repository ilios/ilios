<?php

declare(strict_types=1);

namespace App\Tests;

abstract class ReadEndpointTest extends AbstractEndpointTest implements GetEndpointTestInterface
{
    use GetEndpointTestable;

    protected bool $isGraphQLTestable = true;
}
