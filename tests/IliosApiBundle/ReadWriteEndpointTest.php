<?php

namespace Tests\IliosApiBundle;

/**
 * Class ReadWriteEndpointTest
 * @package Tests\IliosApiBundle
 */
abstract class ReadWriteEndpointTest extends ReadEndpointTest implements
    PostEndpointTestInterface,
    PutEndpointTestInterface,
    DeleteEndpointTestInterface
{
    use PostEndpointTestable;
    use PutEndpointTestable;
    use DeleteEndpointTestable;
}
