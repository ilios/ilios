<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\AddRootUserCommand;
use App\Repository\UserRepository;
use Exception;
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
class AddRootUserCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var m\MockInterface
     */
    protected $userRepository;

    /**
     * @var CommandTester
     */
    protected $commandTester;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = m::mock(UserRepository::class);

        $command = new AddRootUserCommand($this->userRepository);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(AddRootUserCommand::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->userRepository);
        unset($this->commandTester);
    }

    /**
     * @covers \App\Command\AddRootUserCommand::execute
     */
    public function testAddRootUser()
    {
        $userId = 1;
        $user = m::mock('App\Entity\User');

        $this->userRepository->shouldReceive('findOneBy')->with(['id' => $userId])->andReturn($user);
        $this->userRepository->shouldReceive('update');
        $user->shouldReceive('setRoot');

        $this->commandTester->execute([
            'command' => AddRootUserCommand::COMMAND_NAME,
            'userId' => $userId
        ]);

        $this->userRepository->shouldHaveReceived('update', [ $user, true, true ]);
        $user->shouldHaveReceived('setRoot', [ true ]);

        $output = $this->commandTester->getDisplay();

        $this->assertEquals("User with id #{$userId} has been granted root-level privileges.", trim($output));
    }

    /**
     * @covers \App\Command\AddRootUserCommand::execute
     */
    public function testMissingInput()
    {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([
            'command' => AddRootUserCommand::COMMAND_NAME
        ]);
    }

    /**
     * @covers \App\Command\AddRootUserCommand::execute
     */
    public function testUserNotFound()
    {
        $userId = 0;
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => $userId])->andReturn(null);

        $this->expectException(Exception::class);
        $this->commandTester->execute([
            'command' => AddRootUserCommand::COMMAND_NAME,
            'userId' => $userId
        ]);
    }
}
