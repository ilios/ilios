<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\ChangeUsernameCommand;
use App\Entity\AuthenticationInterface;
use App\Entity\UserInterface;
use App\Repository\AuthenticationRepository;
use App\Repository\UserRepository;
use Exception;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class ChangeUsernameCommandTest
 * @package App\Tests\Command
 * @group cli
 */
class ChangeUsernameCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private const COMMAND_NAME = 'ilios:change-username';

    /** @var CommandTester */
    protected $commandTester;

    /** @var m\Mock */
    protected $userRepository;
    /** @var m\Mock */
    protected $authenticationRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = m::mock(UserRepository::class);
        $this->authenticationRepository = m::mock(AuthenticationRepository::class);

        $command = new ChangeUsernameCommand(
            $this->userRepository,
            $this->authenticationRepository
        );
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
        unset($this->userRepository);
        unset($this->authenticationRepository);
        unset($this->commandTester);
    }

    public function testChangeUsername()
    {
        $user = m::mock(UserInterface::class);
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($user);
        $this->commandTester->setInputs(['newname']);

        $authentication = m::mock(AuthenticationInterface::class);
        $user->shouldReceive('getAuthentication')->once()->andReturn($authentication);
        $this->authenticationRepository->shouldReceive('getUsernames')->once()->andReturn([]);

        $authentication->shouldReceive('setUsername')->with('newname')->once();
        $this->authenticationRepository->shouldReceive('update')->with($authentication);

        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Username Changed/',
            $output
        );
    }

    public function testUserWithoutAuthentication()
    {
        $user = m::mock(UserInterface::class);
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($user);
        $this->commandTester->setInputs(['newname']);

        $authentication = m::mock(AuthenticationInterface::class);
        $user->shouldReceive('getAuthentication')->once()->andReturn(null);
        $this->authenticationRepository->shouldReceive('create')->once()->andReturn($authentication);
        $user->shouldReceive('setAuthentication')->once()->with($authentication);
        $this->authenticationRepository->shouldReceive('getUsernames')->once()->andReturn([]);

        $authentication->shouldReceive('setUsername')->with('newname')->once();
        $this->authenticationRepository->shouldReceive('update')->with($authentication);

        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Username Changed/',
            $output
        );
    }

    public function testDuplicationUsername()
    {
        $user = m::mock(UserInterface::class);
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($user);
        $this->commandTester->setInputs(['newname']);

        $this->authenticationRepository->shouldReceive('getUsernames')->once()->andReturn(['newname']);

        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ]);
    }

    public function testDuplicateUsernameCase()
    {
        $user = m::mock(UserInterface::class);
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($user);
        $this->commandTester->setInputs(['newName']);

        $this->authenticationRepository->shouldReceive('getUsernames')->once()->andReturn(['newname']);

        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ]);
    }

    public function testDuplicateUsernameCaseInDB()
    {
        $user = m::mock(UserInterface::class);
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($user);
        $this->commandTester->setInputs(['newname']);

        $this->authenticationRepository->shouldReceive('getUsernames')->once()->andReturn(['newName']);

        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ]);
    }

    public function testCaseIsPreserved()
    {
        $user = m::mock(UserInterface::class);
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($user);
        $this->commandTester->setInputs(['NewName']);

        $authentication = m::mock(AuthenticationInterface::class);
        $user->shouldReceive('getAuthentication')->once()->andReturn(null);
        $this->authenticationRepository->shouldReceive('create')->once()->andReturn($authentication);
        $user->shouldReceive('setAuthentication')->once()->with($authentication);
        $this->authenticationRepository->shouldReceive('getUsernames')->once()->andReturn([]);

        $authentication->shouldReceive('setUsername')->with('NewName')->once();
        $this->authenticationRepository->shouldReceive('update')->with($authentication);

        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Username Changed/',
            $output
        );
    }

    public function testWhitespaceIsTrimmed()
    {
        $user = m::mock(UserInterface::class);
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($user);
        $this->commandTester->setInputs(['  username  ']);

        $authentication = m::mock(AuthenticationInterface::class);
        $user->shouldReceive('getAuthentication')->once()->andReturn(null);
        $this->authenticationRepository->shouldReceive('create')->once()->andReturn($authentication);
        $user->shouldReceive('setAuthentication')->once()->with($authentication);
        $this->authenticationRepository->shouldReceive('getUsernames')->once()->andReturn([]);

        $authentication->shouldReceive('setUsername')->with('username')->once();
        $this->authenticationRepository->shouldReceive('update')->with($authentication);

        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Username Changed/',
            $output
        );
    }

    public function testBadUserId()
    {
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn(null);
        $this->expectException(Exception::class);
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ]);
    }

    public function testUserRequired()
    {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute(['command' => self::COMMAND_NAME]);
    }
}
