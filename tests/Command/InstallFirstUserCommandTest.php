<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Classes\SessionUserInterface;
use App\Repository\AuthenticationRepository;
use App\Repository\SchoolRepository;
use App\Repository\UserRepository;
use App\Service\SessionUserProvider;
use App\Command\InstallFirstUserCommand;
use App\Entity\User;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class InstallFirstUserCommandTest
 * @group cli
 */
class InstallFirstUserCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private const COMMAND_NAME = 'ilios:setup-first-user';

    protected $userRepository;
    protected $authenticationRepository;
    protected $schoolRepository;
    protected $hasher;
    protected $questionHelper;
    protected $sessionUserProvider;

    /**
     * @var CommandTester
     */
    protected $commandTester;

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
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
        $this->questionHelper = $commandInApp->getHelper('question');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->userRepository);
        unset($this->authenticationRepository);
        unset($this->schoolRepository);
        unset($this->commandTester);
        unset($this->questionHelper);
        unset($this->sessionUserProvider);
    }

    public function testExecute()
    {
        $this->getReadyForInput();
        $this->commandTester->execute([
            'command'  => self::COMMAND_NAME,
            '--school' => '1',
            '--email' => 'email@example.com',
        ]);

        $this->checkOuput();
    }

    public function testUserExists()
    {
        $this->userRepository->shouldReceive('findOneBy')->with([])->andReturn(new User());
        $this->schoolRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn(null);
        $this->expectException(Exception::class);
        $this->commandTester->execute([
            'command' => self::COMMAND_NAME,
            '--school' => '1'
        ]);
    }

    public function testBadSchoolId()
    {
        $this->userRepository->shouldReceive('findOneBy')->with([])->andReturn(null);
        $this->schoolRepository->shouldReceive('findBy')->with([], ['title' => 'ASC'])->andReturn([]);
        $this->expectException(Exception::class);
        $this->commandTester->execute([
            'command' => self::COMMAND_NAME,
            '--school' => '1'
        ]);
    }

    public function testAskForMissingSchools()
    {
        $this->getReadyForInput();
        $this->commandTester->setInputs(['0', 'Yes']);
        $this->commandTester->execute([
            'command' => self::COMMAND_NAME,
            '--email' => 'email@example.com',
        ]);
        $this->checkOuput();
    }

    public function testAskForMissingEmail()
    {
        $this->getReadyForInput();
        $this->commandTester->setInputs(['email@example.com', 'Yes']);
        $this->commandTester->execute([
            'command' => self::COMMAND_NAME,
            '--school' => '1',
        ]);
        $this->checkOuput();
    }

    protected function getReadyForInput()
    {
        $school = m::mock('App\Entity\SchoolInterface')
            ->shouldReceive('getId')->andReturn(1)
            ->shouldReceive('getTitle')->andReturn('Big School Title')
            ->mock();
        $sessionUser = m::mock(SessionUserInterface::class);
        $developerRole = m::mock('App\Entity\UserRoleInterface');
        $courseDirectorRole = m::mock('App\Entity\UserRoleInterface');
        $user = m::mock('App\Entity\UserInterface')
            ->shouldReceive('setFirstName')->with('First')
            ->shouldReceive('setLastName')->with('User')
            ->shouldReceive('setMiddleName')
            ->shouldReceive('setEmail')->with('email@example.com')
            ->shouldReceive('setAddedViaIlios')->with(true)
            ->shouldReceive('setEnabled')->with(true)
            ->shouldReceive('setUserSyncIgnore')->with(false)
            ->shouldReceive('setSchool')->with($school)
            ->shouldReceive('setRoot')->with(true)
            ->mock();
        $authentication = m::mock('App\Entity\AuthenticationInterface')
            ->shouldReceive('setUsername')->with('first_user')
            ->shouldReceive('setPasswordHash')->with('hashBlurb')
            ->shouldReceive('getUser')->andReturn($user)
            ->shouldReceive('setUser')
            ->mock();
        $user->shouldReceive('setAuthentication')->with($authentication);
        $this->schoolRepository->shouldReceive('findOneBy')->with(['id' => '1'])->andReturn($school);
        $this->schoolRepository->shouldReceive('findBy')->with([], ['title' => 'ASC'])->andReturn([$school]);
        $this->userRepository->shouldReceive('findOneBy')->with([])->andReturn([]);
        $this->userRepository->shouldReceive('create')->andReturn($user);
        $this->userRepository->shouldReceive('update')->with($user);
        $this->authenticationRepository->shouldReceive('create')->andReturn($authentication);
        $this->authenticationRepository->shouldReceive('update')->with($authentication);
        $this->hasher->shouldReceive('hashPassword')->with($sessionUser, 'Ch4nge_m3')->andReturn('hashBlurb');
        $this->userRepository->shouldReceive('update');
        $this->sessionUserProvider->shouldReceive('createSessionUserFromUser')->with($user)->andReturn($sessionUser);
    }

    protected function checkOuput()
    {
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Success!', $output);
    }
}
