<?php

declare(strict_types=1);

namespace App\Tests;

abstract class ReadEndpointTestCase extends AbstractEndpointTestCase implements GetEndpointTestInterface
{
    use GetEndpointTestable;

    protected bool $isGraphQLTestable = true;
}
