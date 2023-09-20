<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\Endpoints\DeleteEndpointTestInterface as Delete;
use App\Tests\Endpoints\PatchEndpointTestInterface as Patch;
use App\Tests\Endpoints\PostEndpointTestInterface as Post;
use App\Tests\Endpoints\PutEndpointTestInterface as Put;

/**
 * Class AbstractReadWriteEndpoint
 * @package App\Tests
 */
abstract class AbstractReadWriteEndpoint extends AbstractReadEndpoint implements Delete, Patch, Post, Put
{
    use DeleteEndpointTestable;
    use PatchEndpointTestable;
    use PostEndpointTestable;
    use PutEndpointTestable;
}
