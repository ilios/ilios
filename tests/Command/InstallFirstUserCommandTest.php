<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\Attributes\Group;
use App\Classes\SessionUserInterface;
use App\Entity\AuthenticationInterface;
use App\Entity\SchoolInterface;
use App\Entity\UserInterface;
use App\Repository\AuthenticationRepository;
use App\Repository\SchoolRepository;
use App\Repository\UserRepository;
use App\Service\SessionUserProvider;
use App\Command\InstallFirstUserCommand;
use App\Entity\User;
use Exception;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class InstallFirstUserCommandTest
 */
#[Group('cli')]
final class InstallFirstUserCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected m\MockInterface $userRepository;
    protected m\MockInterface $authenticationRepository;
    protected m\MockInterface $schoolRepository;
    protected m\MockInterface $hasher;
    protected m\MockInterface $sessionUserProvider;
    protected CommandTester $commandTester;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = m::mock(UserRepository::class);
        $this->authenticationRepository = m::mock(AuthenticationRepository::class);
        $this->schoolRepository = m::mock(SchoolRepository::class);
        $this->hasher = m::mock(UserPasswordHasherInterface::class);
        $this->sessionUserProvider = m::mock(SessionUserProvider::class);

        $command = new InstallFirstUserCommand(
            $this->userRepository,
            $this->schoolRepository,
            $this->authenticationRepository,
            $this->hasher,
            $this->sessionUserProvider
        );
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
        unset($this->authenticationRepository);
        unset($this->schoolRepository);
        unset($this->hasher);
        unset($this->commandTester);
        unset($this->sessionUserProvider);
    }

    public function testExecute(): void
    {
        $this->getReadyForInput();
        $this->commandTester->execute([
            '--school' => '1',
            '--email' => 'email@example.com',
        ]);

        $this->checkOuput();
    }

    public function testUserExists(): void
    {
        $this->userRepository->shouldReceive('findOneBy')->with([])->andReturn(new User());
        $this->schoolRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn(null);
        $this->expectException(Exception::class);
        $this->commandTester->execute([
            '--school' => '1',
        ]);
    }

    public function testBadSchoolId(): void
    {
        $this->userRepository->shouldReceive('findOneBy')->with([])->andReturn(null);
        $this->schoolRepository->shouldReceive('findBy')->with([], ['title' => 'ASC'])->andReturn([]);
        $this->expectException(Exception::class);
        $this->commandTester->execute([
            '--school' => '1',
        ]);
    }

    public function testAskForMissingSchools(): void
    {
        $this->getReadyForInput();
        $this->commandTester->setInputs(['0', 'Yes']);
        $this->commandTester->execute([
            '--email' => 'email@example.com',
        ]);
        $this->checkOuput();
    }

    public function testAskForMissingEmail(): void
    {
        $this->getReadyForInput();
        $this->commandTester->setInputs(['email@example.com', 'Yes']);
        $this->commandTester->execute([
            '--school' => '1',
        ]);
        $this->checkOuput();
    }

    protected function getReadyForInput(): void
    {
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $school->shouldReceive('getTitle')->andReturn('Big School Title');
        $sessionUser = m::mock(SessionUserInterface::class);
        $user = m::mock(UserInterface::class);
        $user->shouldReceive('setFirstName')->with('First');
        $user->shouldReceive('setLastName')->with('User');
        $user->shouldReceive('setMiddleName');
        $user->shouldReceive('setEmail')->with('email@example.com');
        $user->shouldReceive('setAddedViaIlios')->with(true);
        $user->shouldReceive('setEnabled')->with(true);
        $user->shouldReceive('setUserSyncIgnore')->with(false);
        $user->shouldReceive('setSchool')->with($school);
        $user->shouldReceive('setRoot')->with(true);

        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('setUsername')->with('first_user');
        $authentication->shouldReceive('setPasswordHash')->with('hashBlurb');
        $authentication->shouldReceive('getUser')->andReturn($user);
        $authentication->shouldReceive('setUser');

        $user->shouldReceive('setAuthentication')->with($authentication);
        $this->schoolRepository->shouldReceive('findOneBy')->with(['id' => '1'])->andReturn($school);
        $this->schoolRepository->shouldReceive('findBy')->with([], ['title' => 'ASC'])->andReturn([$school]);
        $this->userRepository->shouldReceive('findOneBy')->with([])->andReturn(null);
        $this->userRepository->shouldReceive('create')->andReturn($user);
        $this->userRepository->shouldReceive('update')->with($user);
        $this->authenticationRepository->shouldReceive('create')->andReturn($authentication);
        $this->authenticationRepository->shouldReceive('update')->with($authentication);
        $this->hasher->shouldReceive('hashPassword')->with($sessionUser, 'Ch4nge_m3')->andReturn('hashBlurb');
        $this->userRepository->shouldReceive('update');
        $this->sessionUserProvider->shouldReceive('createSessionUserFromUser')->with($user)->andReturn($sessionUser);
    }

    protected function checkOuput(): void
    {
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Success!', $output);
    }
}
