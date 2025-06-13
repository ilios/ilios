<?php

declare(strict_types=1);

namespace App\Tests\MessageHandler;

use App\Classes\IndexableCourse;
use App\Message\CourseIndexRequest;
use App\MessageHandler\CourseIndexHandler;
use App\Repository\CourseRepository;
use App\Service\Index\Curriculum;
use App\Tests\TestCase;
use Mockery as m;
use Symfony\Component\Messenger\MessageBusInterface;

final class CourseIndexHandlerTest extends TestCase
{
    protected m\MockInterface|Curriculum $index;
    protected m\MockInterface|CourseRepository $repository;
    protected m\MockInterface|MessageBusInterface $bus;

    public function setUp(): void
    {
        parent::setUp();
        $this->index = m::mock(Curriculum::class);
        $this->repository = m::mock(CourseRepository::class);
        $this->bus = m::mock(MessageBusInterface::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->index);
        unset($this->repository);
        unset($this->bus);
    }

    public function testInvoke(): void
    {
        $firstCourse = m::mock(IndexableCourse::class);
        $secondCourse = m::mock(IndexableCourse::class);
        $handler = new CourseIndexHandler($this->index, $this->repository, $this->bus);
        $request = new CourseIndexRequest([6, 24]);

        $this->repository->shouldReceive(('getCourseIndexesFor'))
            ->once()
            ->with([6, 24])
            ->andReturn([
                $firstCourse,
                $secondCourse,
            ]);

        $this->index
            ->shouldReceive('index')
            ->once()
            ->withArgs(function (array $courses) use ($firstCourse, $secondCourse) {
                $this->assertCount(2, $courses);
                $this->assertContains($firstCourse, $courses);
                $this->assertContains($secondCourse, $courses);

                $this->assertEquals([$firstCourse, $secondCourse], $courses);

                return true;
            });

        $handler->__invoke($request);
    }
}
