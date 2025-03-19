<?php

declare(strict_types=1);

namespace App\Tests\Message;

use App\Message\SessionDeleteRequest;
use App\Tests\TestCase;

class SessionDeleteRequestTest extends TestCase
{
    public function testItWorks(): void
    {
        $id = 42;
        $request = new SessionDeleteRequest($id);
        $this->assertSame($id, $request->getSessionId());
    }
}
