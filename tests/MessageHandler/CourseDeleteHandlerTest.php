<?php

declare(strict_types=1);

namespace App\Tests\MessageHandler;

use App\Message\CourseDeleteRequest;
use App\MessageHandler\CourseDeleteHandler;
use App\Service\Index\Curriculum;
use App\Tests\TestCase;
use Mockery as m;

class CourseDeleteHandlerTest extends TestCase
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
        $handler = new CourseDeleteHandler($this->curriculum);
        $request = new CourseDeleteRequest(24);

        $this->curriculum
            ->shouldReceive('deleteCourse')
            ->once()
            ->with(24);

        $handler->__invoke($request);
    }
}
