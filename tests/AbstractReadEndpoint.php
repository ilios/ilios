<?php

declare(strict_types=1);

namespace App\Tests;

abstract class AbstractReadEndpoint extends AbstractEndpoint implements GetEndpointTestInterface
{
    use GetEndpointTestable;

    protected bool $isGraphQLTestable = true;
}
