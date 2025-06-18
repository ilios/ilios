<?php

declare(strict_types=1);

namespace App\Tests\MessageHandler;

use App\Message\CourseIndexRequest;
use App\MessageHandler\CourseIndexHandler;
use App\Service\Index\Curriculum;
use App\Tests\TestCase;
use Exception;
use Mockery as m;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class CourseIndexHandlerTest extends TestCase
{
    protected m\MockInterface|Curriculum $index;
    protected m\MockInterface|MessageBusInterface $bus;

    public function setUp(): void
    {
        parent::setUp();
        $this->index = m::mock(Curriculum::class);
        $this->bus = m::mock(MessageBusInterface::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->index);
        unset($this->bus);
    }

    public function testInvoke(): void
    {
        $handler = new CourseIndexHandler($this->index, $this->bus);
        $request = new CourseIndexRequest([6, 24]);

        $this->index
            ->shouldReceive('index')
            ->once()
            ->withArgs(function (array $courses) {
                $this->assertCount(2, $courses);
                $this->assertEquals([6, 24], $courses);

                return true;
            });

        $handler->__invoke($request);
    }
    public function testExceptionSplittingForMultipleCourses(): void
    {
        $handler = new CourseIndexHandler($this->index, $this->bus);
        $request = new CourseIndexRequest([6, 24]);

        $this->index
            ->shouldReceive('index')
            ->once()
            ->andThrow(Exception::class);

        $this->bus
            ->shouldReceive('dispatch')
            ->withArgs(fn (CourseIndexRequest $request) => $request->getCourseIds() === [6])
            ->andReturn(new Envelope(new stdClass()))
            ->once();

        $this->bus
            ->shouldReceive('dispatch')
            ->withArgs(fn (CourseIndexRequest $request) => $request->getCourseIds() === [24])
            ->andReturn(new Envelope(new stdClass()))
            ->once();

        $handler->__invoke($request);
    }
    public function testExceptionThrowsForSingleCourse(): void
    {
        $handler = new CourseIndexHandler($this->index, $this->bus);
        $request = new CourseIndexRequest([13]);

        $this->index
            ->shouldReceive('index')
            ->once()
            ->andThrow(Exception::class);

        $this->expectException(Exception::class);

        $handler->__invoke($request);
    }
}
