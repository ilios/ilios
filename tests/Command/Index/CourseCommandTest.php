<?php

declare(strict_types=1);

namespace App\Tests\Command\Index;

use App\Classes\IndexableCourse;
use App\Command\Index\CourseCommand;
use App\Repository\CourseRepository;
use App\Service\Index\Curriculum;
use DateTime;
use PHPUnit\Framework\Attributes\Group;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

#[Group('cli')]
final class CourseCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected CommandTester $commandTester;
    protected m\MockInterface | CourseRepository $repository;
    protected m\MockInterface | Curriculum $index;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = m::mock(CourseRepository::class);
        $this->index = m::mock(Curriculum::class);

        $command = new CourseCommand($this->repository, $this->index);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find($command->getName());
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * Remove all mock objects
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->repository);
        unset($this->index);
        unset($this->commandTester);
    }

    public function testExecute(): void
    {
        $this->index->shouldReceive('isEnabled')->once()->andReturn(true);
        $c = m::mock(IndexableCourse::class);
        $this->repository->shouldReceive('getCourseIndexesFor')->once()->with([13])->andReturn([$c]);
        $this->index->shouldReceive('index')
            ->once()
            ->withArgs(
                fn (array $a, DateTime $dateTime) => $c === $a[0] && $dateTime->diff(new DateTime())->days === 0
            );
        $this->commandTester->execute(['courseId' => 13]);

        $this->commandTester->assertCommandIsSuccessful();
    }

    public function testExecuteWithIndexDisabled(): void
    {
        $this->index->shouldReceive('isEnabled')->once()->andReturn(false);
        $this->index->shouldNotReceive('index');

        $this->commandTester->execute(['courseId' => 1]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Indexing is not currently configured./',
            $output
        );
    }
}
