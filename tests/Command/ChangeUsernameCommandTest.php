<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\Attributes\Group;
use App\Command\ChangeUsernameCommand;
use App\Entity\AuthenticationInterface;
use App\Entity\UserInterface;
use App\Repository\AuthenticationRepository;
use App\Repository\UserRepository;
use Exception;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class ChangeUsernameCommandTest
 * @package App\Tests\Command
 */
#[Group('cli')]
final class ChangeUsernameCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected CommandTester $commandTester;
    protected m\MockInterface $userRepository;
    protected m\MockInterface $authenticationRepository;

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
        $commandInApp = $application->find($command->getName());
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

    public function testChangeUsername(): void
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
            'userId' => '1',
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Username Changed/',
            $output
        );
    }

    public function testUserWithoutAuthentication(): void
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
            'userId' => '1',
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Username Changed/',
            $output
        );
    }

    public function testDuplicationUsername(): void
    {
        $user = m::mock(UserInterface::class);
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($user);
        $this->commandTester->setInputs(['newname']);

        $this->authenticationRepository->shouldReceive('getUsernames')->once()->andReturn(['newname']);

        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([
            'userId' => '1',
        ]);
    }

    public function testDuplicateUsernameCase(): void
    {
        $user = m::mock(UserInterface::class);
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($user);
        $this->commandTester->setInputs(['newName']);

        $this->authenticationRepository->shouldReceive('getUsernames')->once()->andReturn(['newname']);

        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([
            'userId' => '1',
        ]);
    }

    public function testDuplicateUsernameCaseInDB(): void
    {
        $user = m::mock(UserInterface::class);
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($user);
        $this->commandTester->setInputs(['newname']);

        $this->authenticationRepository->shouldReceive('getUsernames')->once()->andReturn(['newName']);

        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([
            'userId' => '1',
        ]);
    }

    public function testCaseIsPreserved(): void
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
            'userId' => '1',
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Username Changed/',
            $output
        );
    }

    public function testWhitespaceIsTrimmed(): void
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
            'userId' => '1',
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Username Changed/',
            $output
        );
    }

    public function testBadUserId(): void
    {
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn(null);
        $this->expectException(Exception::class);
        $this->commandTester->execute([
            'userId' => '1',
        ]);
    }

    public function testUserRequired(): void
    {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([]);
    }
}
