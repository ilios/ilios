<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Command\RemoveRootUserCommand;
use App\Entity\UserInterface;
use App\Repository\UserRepository;
use Exception;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Tests the Remove Root User command.
 *
 * Class RemoveRootUserCommandTest
 */
#[Group('cli')]
#[CoversClass(RemoveRootUserCommand::class)]
final class RemoveRootUserCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected m\MockInterface $userRepository;
    protected CommandTester $commandTester;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = m::mock(UserRepository::class);

        $command = new RemoveRootUserCommand($this->userRepository);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->addCommands([$command]);
        $commandInApp = $application->find($command->getName());
        $this->commandTester = new CommandTester($commandInApp);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->userRepository);
        unset($this->commandTester);
    }

    public function testRemoveRootUser(): void
    {
        $userId = 1;
        $user = m::mock(UserInterface::class);

        $this->userRepository->shouldReceive('findOneBy')->with(['id' => $userId])->andReturn($user);
        $this->userRepository->shouldReceive('update');
        $user->shouldReceive('setRoot');

        $this->commandTester->execute([
            'userId' => $userId,
        ]);

        $this->userRepository->shouldHaveReceived('update', [ $user, true, true ]);
        $user->shouldHaveReceived('setRoot', [ false ]);

        $output = $this->commandTester->getDisplay();

        $this->assertEquals("Root-level privileges have been revoked from user with id #{$userId}.", trim($output));
    }

    public function testMissingInput(): void
    {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([]);
    }

    public function testUserNotFound(): void
    {
        $userId = 0;
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => $userId])->andReturn(null);

        $this->expectException(Exception::class);
        $this->commandTester->execute([
            'userId' => $userId,
        ]);
    }
}
