<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\Attributes\Group;
use App\Command\SetSchoolConfigValueCommand;
use App\Entity\SchoolConfig;
use App\Entity\SchoolInterface;
use App\Repository\SchoolConfigRepository;
use App\Repository\SchoolRepository;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class SetSchoolConfigValueCommandTest
 * @package App\Tests\Command
 */
#[Group('cli')]
final class SetSchoolConfigValueCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected CommandTester $commandTester;
    protected m\MockInterface $schoolRepository;
    protected m\MockInterface $schoolConfigRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->schoolRepository = m::mock(SchoolRepository::class);
        $this->schoolConfigRepository = m::mock(SchoolConfigRepository::class);
        $command = new SetSchoolConfigValueCommand($this->schoolRepository, $this->schoolConfigRepository);
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
        unset($this->schoolRepository);
        unset($this->schoolConfigRepository);
        unset($this->commandTester);
    }

    public function testSaveExistingConfig(): void
    {
        $mockSchool = m::mock(SchoolInterface::class);
        $mockSchool->shouldReceive('getId')->once()->andReturn(1);
        $this->schoolRepository->shouldReceive('findOneBy')->once()->with(['id' => '1'])->andReturn($mockSchool);
        $mockConfig = m::mock(SchoolConfig::class);
        $mockConfig->shouldReceive('setValue')->with('bar')->once();
        $this->schoolConfigRepository->shouldReceive('findOneBy')
            ->with(['school' => '1', 'name' => 'foo'])
            ->once()
            ->andReturn($mockConfig);
        $this->schoolConfigRepository->shouldReceive('update')->with($mockConfig, true)->once();

        $this->commandTester->execute([
            'school' => '1',
            'name' => 'foo',
            'value' => 'bar',
        ]);
    }

    public function testSaveNewConfig(): void
    {
        $mockSchool = m::mock(SchoolInterface::class);
        $mockSchool->shouldReceive('getId')->once()->andReturn(1);
        $this->schoolRepository->shouldReceive('findOneBy')->once()->with(['id' => '1'])->andReturn($mockSchool);
        $mockConfig = m::mock(SchoolConfig::class);
        $mockConfig->shouldReceive('setValue')->with('bar')->once();
        $mockConfig->shouldReceive('setSchool')->with($mockSchool)->once();
        $mockConfig->shouldReceive('setName')->with('foo')->once();
        $this->schoolConfigRepository
            ->shouldReceive('findOneBy')
            ->with(['school' => '1', 'name' => 'foo'])
            ->once()
            ->andReturn(null);
        $this->schoolConfigRepository->shouldReceive('create')->once()->andReturn($mockConfig);
        $this->schoolConfigRepository->shouldReceive('update')->with($mockConfig, true)->once();

        $this->commandTester->execute([
            'school' => '1',
            'name' => 'foo',
            'value' => 'bar',
        ]);
    }

    public function testNameRequired(): void
    {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([
            'school' => '1',
            'value' => 'bar',
        ]);
    }

    public function testValueRequired(): void
    {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([
            'school' => '1',
            'name' => 'foo',
        ]);
    }

    public function testSchoolRequired(): void
    {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([
            'name' => 'foo',
            'value' => 'bar',
        ]);
    }
}
