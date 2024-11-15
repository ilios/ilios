<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Command\DeleteServiceTokenCommand;
use App\Entity\ServiceTokenInterface;
use App\Repository\ServiceTokenRepository;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * @package App\Tests\Command
 */
#[Group('cli')]
#[CoversClass(DeleteServiceTokenCommand::class)]
class DeleteServiceTokenCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

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
        $commandInApp = $application->find($command->getName());
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
