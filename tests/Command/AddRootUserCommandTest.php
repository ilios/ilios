<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\AddRootUserCommand;
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
 * Tests the Add Root User command.
 *
 * Class AddRootUserCommandTest
 * @group cli
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Command\AddRootUserCommand::class)]
class AddRootUserCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected m\MockInterface $userRepository;
    protected CommandTester $commandTester;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = m::mock(UserRepository::class);

        $command = new AddRootUserCommand($this->userRepository);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find($command->getName());
        $this->commandTester = new CommandTester($commandInApp);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->userRepository);
        unset($this->commandTester);
    }

    public function testAddRootUser(): void
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
        $user->shouldHaveReceived('setRoot', [ true ]);

        $output = $this->commandTester->getDisplay();

        $this->assertEquals("User with id #{$userId} has been granted root-level privileges.", trim($output));
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
