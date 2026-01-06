<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\Attributes\Group;
use App\Command\RolloverCourseCommand;
use App\Entity\Course;
use App\Service\CourseRollover;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class RolloverCourseCommandTest
 */
#[Group('cli')]
final class RolloverCourseCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected m\MockInterface $service;
    protected CommandTester $commandTester;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = m::mock(CourseRollover::class);

        $command = new RolloverCourseCommand($this->service);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->addCommands([$command]);
        $commandInApp = $application->find($command->getName());
        $this->commandTester = new CommandTester($commandInApp);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->service);
        unset($this->commandTester);
    }

    public function testCommandFailsWithoutArguments(): void
    {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([]);
    }

    public function testCommandPassesArgumentsAndDefaultOptions(): void
    {
        $courseId  = '1';
        $newAcademicYear = '2017';
        $newCourseId = 5;
        $this->service->shouldReceive('rolloverCourse')->andReturnUsing(function () use ($newCourseId) {
            $course = new Course();
            $course->setId($newCourseId);
            return $course;
        });
        $this->commandTester->execute([
            'courseId' => $courseId,
            'newAcademicYear' => $newAcademicYear,
        ]);

        $defaultOptions = [
            'new-start-date' => null,
            'skip-course-learning-materials' => false,
            'skip-course-objectives' => false,
            'skip-course-terms' => false,
            'skip-course-mesh' => false,
            'skip-sessions' => false,
            'skip-session-learning-materials' => false,
            'skip-session-objectives' => false,
            'skip-session-terms' => false,
            'skip-session-mesh' => false,
            'skip-offerings' => false,
            'skip-instructors' => false,
            'skip-instructor-groups' => false,
            'new-course-title' => null,
        ];

        $this->service
            ->shouldHaveReceived('rolloverCourse')
            ->withArgs([$courseId, $newAcademicYear, $defaultOptions])
            ->once();
    }

    public function testCommandPassesUserProvidedOptions(): void
    {
        $customOptions = [
            'new-start-date' => '2016-03-12',
            'skip-course-learning-materials' => true,
            'skip-course-objectives' => true,
            'skip-course-terms' => true,
            'skip-course-mesh' => true,
            'skip-sessions' => true,
            'skip-session-learning-materials' => true,
            'skip-session-objectives' => true,
            'skip-session-terms' => true,
            'skip-session-mesh' => true,
            'skip-offerings' => true,
            'skip-instructors' => true,
            'skip-instructor-groups' => true,
            'new-course-title' => 'lorem ipsum',
        ];

        $courseId  = '1';
        $newAcademicYear = '2017';
        $newCourseId = 5;
        $this->service->shouldReceive('rolloverCourse')->andReturnUsing(function () use ($newCourseId) {
            $course = new Course();
            $course->setId($newCourseId);
            return $course;
        });
        $commandOptions = [
            'courseId' => $courseId,
            'newAcademicYear' => $newAcademicYear,
        ];

        foreach ($customOptions as $name => $value) {
            $commandOptions['--' . $name] = $value;
        }

        $this->commandTester->execute($commandOptions);

        $this->service
            ->shouldHaveReceived('rolloverCourse')
            ->withArgs([$courseId, $newAcademicYear, $customOptions])
            ->once();
    }

    public function testCommandPrintsOutNewCourseIdOnSuccess(): void
    {
        $courseId  = '1';
        $newAcademicYear = '2017';
        $newCourseId = 5;
        $this->service->shouldReceive('rolloverCourse')->andReturnUsing(function () use ($newCourseId) {
            $course = new Course();
            $course->setId($newCourseId);
            return $course;
        });
        $this->commandTester->execute([
            'courseId' => $courseId,
            'newAcademicYear' => $newAcademicYear,
        ]);

        $this->service
            ->shouldHaveReceived('rolloverCourse')
            ->withAnyArgs()
            ->once();

        $output = $this->commandTester->getDisplay();
        $this->assertEquals(
            "This course has been rolled over. The new course id is {$newCourseId}.",
            trim($output)
        );
    }
}
