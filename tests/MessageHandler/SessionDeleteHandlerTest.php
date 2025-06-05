<?php

declare(strict_types=1);

namespace App\Tests\MessageHandler;

use App\Message\SessionDeleteRequest;
use App\MessageHandler\SessionDeleteHandler;
use App\Service\Index\Curriculum;
use App\Tests\TestCase;
use Mockery as m;

final class SessionDeleteHandlerTest extends TestCase
{
    protected m\MockInterface|Curriculum $curriculum;

    public function setUp(): void
    {
        parent::setUp();
        $this->curriculum = m::mock(Curriculum::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->curriculum);
    }

    public function testInvoke(): void
    {
        $handler = new SessionDeleteHandler($this->curriculum);
        $request = new SessionDeleteRequest(24);

        $this->curriculum
            ->shouldReceive('deleteSession')
            ->once()
            ->with(24);

        $handler->__invoke($request);
    }
}
