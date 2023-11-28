<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\DeleteServiceTokenCommand;
use App\Entity\ServiceTokenInterface;
use App\Repository\ServiceTokenRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * @package App\Tests\Command
 * @group cli
 * @covers \App\Command\DeleteServiceTokenCommand
 */
class DeleteServiceTokenCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private const COMMAND_NAME = 'ilios:service-token:delete';

    protected CommandTester $commandTester;
    protected m\MockInterface $serviceTokenRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->serviceTokenRepository = m::mock(ServiceTokenRepository::class);
        $command = new DeleteServiceTokenCommand($this->serviceTokenRepository);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->commandTester);
        unset($this->serviceTokenRepository);
    }

    public function testDeleteToken(): void
    {
        $tokenId = 10;
        $serviceTokenMock = m::mock(ServiceTokenInterface::class);
        $this->serviceTokenRepository->shouldReceive('findOneById')->with($tokenId)->andReturn($serviceTokenMock);
        $this->serviceTokenRepository->shouldReceive('delete')->with($serviceTokenMock);

        $this->commandTester->execute([
            'command' => self::COMMAND_NAME,
            DeleteServiceTokenCommand::ID_KEY => (string) $tokenId,
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringStartsWith(
            "Success! Token with id #{$tokenId} was deleted.",
            $output
        );
        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
    }

    public function testDeleteTokenFailsOnNoTokenFound(): void
    {
        $tokenId = 10;
        $this->serviceTokenRepository->shouldReceive('findOneById')->with($tokenId)->andReturn(null);

        $this->commandTester->execute([
            'command' => self::COMMAND_NAME,
            DeleteServiceTokenCommand::ID_KEY => (string) $tokenId,
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringStartsWith(
            "No service token with id #{$tokenId} was found.",
            $output
        );
        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());
    }
}
