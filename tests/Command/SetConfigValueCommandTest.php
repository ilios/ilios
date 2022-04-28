<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\SetConfigValueCommand;
use App\Entity\ApplicationConfig;
use App\Repository\ApplicationConfigRepository;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class SetConfigValueCommandTest
 * @package App\Tests\Command
 * @group cli
 */
class SetConfigValueCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private const COMMAND_NAME = 'ilios:set-config-value';

    protected $commandTester;
    protected $applicationConfigRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->applicationConfigRepository = m::mock(ApplicationConfigRepository::class);
        $command = new SetConfigValueCommand($this->applicationConfigRepository);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
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

    public function testSaveExistingConfig()
    {
        $mockConfig = m::mock(ApplicationConfig::class);
        $mockConfig->shouldReceive('setValue')->with('bar')->once();
        $this->applicationConfigRepository->shouldReceive('findOneBy')
            ->with(['name' => 'foo'])
            ->once()
            ->andReturn($mockConfig);
        $this->applicationConfigRepository->shouldReceive('update')->with($mockConfig, true)->once();

        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'name'         => 'foo',
            'value'        => 'bar',
        ]);
    }

    public function testSaveNewConfig()
    {
        $mockConfig = m::mock(ApplicationConfig::class);
        $mockConfig->shouldReceive('setValue')->with('bar')->once();
        $mockConfig->shouldReceive('setName')->with('foo')->once();
        $this->applicationConfigRepository->shouldReceive('findOneBy')->with(['name' => 'foo'])
            ->once()->andReturn(null);
        $this->applicationConfigRepository->shouldReceive('create')->once()->andReturn($mockConfig);
        $this->applicationConfigRepository->shouldReceive('update')->with($mockConfig, true)->once();

        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'name'         => 'foo',
            'value'        => 'bar',
        ]);
    }

    public function testNameRequired()
    {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'value'        => 'bar',
        ]);
    }

    public function testValueRequiredIfNotRemoving()
    {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'name'         => 'foo',
        ]);
    }

    public function testRemoveExistingConfig()
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
                'command'      => self::COMMAND_NAME,
                'name'         => 'foo',
                '--remove'       => true,
            ]
        );
    }
    public function testRemoveNonExistentConfig()
    {
        $this->applicationConfigRepository->shouldReceive('findOneBy')
            ->with(['name' => 'foo'])
            ->once()
            ->andReturn(null);

        $this->commandTester->execute(
            [
                'command'      => self::COMMAND_NAME,
                'name'         => 'foo',
                '--remove'       => true,
            ]
        );
    }
}
