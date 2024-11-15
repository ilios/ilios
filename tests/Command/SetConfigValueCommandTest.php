<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\Attributes\Group;
use App\Command\SetConfigValueCommand;
use App\Entity\ApplicationConfig;
use App\Repository\ApplicationConfigRepository;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class SetConfigValueCommandTest
 * @package App\Tests\Command
 */
#[Group('cli')]
class SetConfigValueCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected CommandTester $commandTester;
    protected m\MockInterface $applicationConfigRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->applicationConfigRepository = m::mock(ApplicationConfigRepository::class);
        $command = new SetConfigValueCommand($this->applicationConfigRepository);
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
        unset($this->applicationConfigRepository);
        unset($this->commandTester);
    }

    public function testSaveExistingConfig(): void
    {
        $mockConfig = m::mock(ApplicationConfig::class);
        $mockConfig->shouldReceive('setValue')->with('bar')->once();
        $this->applicationConfigRepository->shouldReceive('findOneBy')
            ->with(['name' => 'foo'])
            ->once()
            ->andReturn($mockConfig);
        $this->applicationConfigRepository->shouldReceive('update')->with($mockConfig, true)->once();

        $this->commandTester->execute([
            'name' => 'foo',
            'value' => 'bar',
        ]);
    }

    public function testSaveNewConfig(): void
    {
        $mockConfig = m::mock(ApplicationConfig::class);
        $mockConfig->shouldReceive('setValue')->with('bar')->once();
        $mockConfig->shouldReceive('setName')->with('foo')->once();
        $this->applicationConfigRepository->shouldReceive('findOneBy')->with(['name' => 'foo'])
            ->once()->andReturn(null);
        $this->applicationConfigRepository->shouldReceive('create')->once()->andReturn($mockConfig);
        $this->applicationConfigRepository->shouldReceive('update')->with($mockConfig, true)->once();

        $this->commandTester->execute([
            'name' => 'foo',
            'value' => 'bar',
        ]);
    }

    public function testNameRequired(): void
    {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([
            'value' => 'bar',
        ]);
    }

    public function testValueRequiredIfNotRemoving(): void
    {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([
            'name' => 'foo',
        ]);
    }

    public function testRemoveExistingConfig(): void
    {
        $mockConfig = m::mock(ApplicationConfig::class);
        $this->applicationConfigRepository->shouldReceive('findOneBy')
            ->with(['name' => 'foo'])
            ->once()
            ->andReturn($mockConfig);
        $this->applicationConfigRepository->shouldReceive('delete')
            ->with($mockConfig)
            ->once();

        $this->commandTester->execute(
            [
                'name' => 'foo',
                '--remove' => true,
            ]
        );
    }
    public function testRemoveNonExistentConfig(): void
    {
        $this->applicationConfigRepository->shouldReceive('findOneBy')
            ->with(['name' => 'foo'])
            ->once()
            ->andReturn(null);

        $this->commandTester->execute(
            [
                'name' => 'foo',
                '--remove' => true,
            ]
        );
    }
}
