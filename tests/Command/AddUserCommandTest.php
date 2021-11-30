<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Classes\SessionUserInterface;
use App\Repository\AuthenticationRepository;
use App\Repository\SchoolRepository;
use App\Repository\UserRepository;
use App\Service\SessionUserProvider;
use App\Command\AddUserCommand;
use App\Entity\AuthenticationInterface;
use App\Entity\SchoolInterface;
use App\Tests\Helper\TestQuestionHelper;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class AddUserCommandTest
 * @group cli
 */
class AddUserCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private const COMMAND_NAME = 'ilios:add-user';

    protected $userRepository;
    protected $authenticationRepository;
    protected $schoolRepository;
    protected $hasher;
    protected $commandTester;
    protected $questionHelper;
    protected $sessionUserProvider;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = m::mock(UserRepository::class);
        $this->authenticationRepository = m::mock(AuthenticationRepository::class);
        $this->schoolRepository = m::mock(SchoolRepository::class);
        $this->hasher = m::mock(UserPasswordHasherInterface::class);
        $this->sessionUserProvider = m::mock(SessionUserProvider::class);


        $command = new AddUserCommand(
            $this->userRepository,
            $this->authenticationRepository,
            $this->schoolRepository,
            $this->hasher,
            $this->sessionUserProvider
        );

        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);

        // Override the question helper to fix testing issue with hidden password input
        $helper = new TestQuestionHelper();
        $commandInApp->getHelperSet()->set($helper, 'question');
        $this->commandTester = new CommandTester($commandInApp);
        $this->questionHelper = $command->getHelper('question');
    }

    /**
     * Remove all mock objects
     */
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
        $this->commandTester->setInputs(['Yes']);

        $this->commandTester->execute(
            [
                'command' => self::COMMAND_NAME,
                '--schoolId' => '1',
                '--firstName' => 'first',
                '--lastName' => 'last',
                '--email' => 'email@example.com',
                '--telephoneNumber' => 'phone',
                '--campusId' => 'abc',
                '--username' => 'abc123',
                '--password' => 'abc123pass',
                '--isRoot' => 'yes',
            ]
        );

        $this->checkOuput();
    }


    public function testBadSchoolId()
    {
        $this->userRepository->shouldReceive('findOneBy')->with(['campusId' => 1])->andReturn(null);
        $this->schoolRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn(null);
        $this->expectException(\Exception::class);
        $this->commandTester->execute(
            [
                'command' => self::COMMAND_NAME,
                '--schoolId' => '1',
            ]
        );
    }

    public function testAskForMissingFirstName()
    {
        $this->getReadyForInput();

        $this->commandTester->setInputs(['first', 'Yes']);

        $this->commandTester->execute(
            [
                'command' => self::COMMAND_NAME,
                '--schoolId' => '1',
                '--lastName' => 'last',
                '--email' => 'email@example.com',
                '--telephoneNumber' => 'phone',
                '--campusId' => 'abc',
                '--username' => 'abc123',
                '--password' => 'abc123pass',
                '--isRoot' => 'yes',
            ]
        );

        $this->checkOuput();
    }

    public function testAskForMissingLastName()
    {
        $this->getReadyForInput();

        $this->commandTester->setInputs(['last', 'Yes']);

        $this->commandTester->execute(
            [
                'command' => self::COMMAND_NAME,
                '--schoolId' => '1',
                '--firstName' => 'first',
                '--email' => 'email@example.com',
                '--telephoneNumber' => 'phone',
                '--campusId' => 'abc',
                '--username' => 'abc123',
                '--password' => 'abc123pass',
                '--isRoot' => 'yes',
            ]
        );

        $this->checkOuput();
    }

    public function testAskForMissingEmail()
    {
        $this->getReadyForInput();

        $this->commandTester->setInputs(['email@example.com', 'Yes']);

        $this->commandTester->execute(
            [
                'command' => self::COMMAND_NAME,
                '--schoolId' => '1',
                '--firstName' => 'first',
                '--lastName' => 'last',
                '--telephoneNumber' => 'phone',
                '--campusId' => 'abc',
                '--username' => 'abc123',
                '--password' => 'abc123pass',
                '--isRoot' => 'yes',
            ]
        );

        $this->checkOuput();
    }

    public function testAskIfIsRoot()
    {
        $this->getReadyForInput();

        $this->commandTester->setInputs(['Yes', 'Yes']);

        $this->commandTester->execute(
            [
                'command' => self::COMMAND_NAME,
                '--schoolId' => '1',
                '--firstName' => 'first',
                '--email' => 'email@example.com',
                '--lastName' => 'last',
                '--telephoneNumber' => 'phone',
                '--campusId' => 'abc',
                '--username' => 'abc123',
                '--password' => 'abc123pass',
            ]
        );

        $this->checkOuput();
    }

    public function testAskForMissingPhone()
    {
        $this->getReadyForInput();

        $this->commandTester->setInputs(['phone', 'Yes']);

        $this->commandTester->execute(
            [
                'command' => self::COMMAND_NAME,
                '--schoolId' => '1',
                '--firstName' => 'first',
                '--lastName' => 'last',
                '--email' => 'email@example.com',
                '--campusId' => 'abc',
                '--username' => 'abc123',
                '--password' => 'abc123pass',
                '--isRoot' => 'yes',
            ]
        );

        $this->checkOuput();
    }

    public function testAskForMissingCampusId()
    {
        $this->getReadyForInput();

        $this->commandTester->setInputs(['abc', 'Yes']);

        $this->commandTester->execute(
            [
                'command' => self::COMMAND_NAME,
                '--schoolId' => '1',
                '--firstName' => 'first',
                '--lastName' => 'last',
                '--email' => 'email@example.com',
                '--telephoneNumber' => 'phone',
                '--username' => 'abc123',
                '--password' => 'abc123pass',
                '--isRoot' => 'yes',
            ]
        );

        $this->checkOuput();
    }

    public function testAskForMissingUsername()
    {
        $this->getReadyForInput();

        $this->commandTester->setInputs(['abc123', 'Yes']);

        $this->commandTester->execute(
            [
                'command' => self::COMMAND_NAME,
                '--schoolId' => '1',
                '--firstName' => 'first',
                '--lastName' => 'last',
                '--email' => 'email@example.com',
                '--telephoneNumber' => 'phone',
                '--campusId' => 'abc',
                '--password' => 'abc123pass',
                '--isRoot' => 'yes',
            ]
        );

        $this->checkOuput();
    }

    public function testAskForMissingPassword()
    {
        $this->getReadyForInput();

        $this->commandTester->setInputs(['abc123pass', 'Yes']);

        $this->commandTester->execute(
            [
                'command' => self::COMMAND_NAME,
                '--schoolId' => '1',
                '--firstName' => 'first',
                '--lastName' => 'last',
                '--email' => 'email@example.com',
                '--telephoneNumber' => 'phone',
                '--campusId' => 'abc',
                '--username' => 'abc123',
                '--isRoot' => 'yes',
            ]
        );

        $this->checkOuput();
    }

    protected function getReadyForInput()
    {
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getTitle')->andReturn('Big School Title');
        $sessionUser = m::mock(SessionUserInterface::class);
        $user = m::mock('App\Entity\UserInterface')
            ->shouldReceive('setFirstName')->with('first')
            ->shouldReceive('setLastName')->with('last')
            ->shouldReceive('setEmail')->with('email@example.com')
            ->shouldReceive('setPhone')->with('phone')
            ->shouldReceive('setCampusId')->with('abc')
            ->shouldReceive('setAddedViaIlios')->with(true)
            ->shouldReceive('setEnabled')->with(true)
            ->shouldReceive('setUserSyncIgnore')->with(false)
            ->shouldReceive('setSchool')->with($school)
            ->shouldReceive('getId')->andReturn(1)
            ->shouldReceive('getFirstAndLastName')->andReturn('Test Person')
            ->shouldReceive('setRoot')->with(true)
            ->mock();

        $authentication = m::mock(AuthenticationInterface::class)
            ->shouldReceive('setUsername')->with('abc123')
            ->shouldReceive('setPasswordHash')->with('hashBlurb')
            ->shouldReceive('getUser')->andReturn($user)
            ->mock();

        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('setAuthentication')->with($authentication);


        $this->hasher->shouldReceive('hashPassword')->with($sessionUser, 'abc123pass')->andReturn('hashBlurb');

        $this->userRepository->shouldReceive('findOneBy')->with(['campusId' => 'abc'])->andReturn(false);
        $this->userRepository->shouldReceive('findOneBy')->with(['email' => 'email@example.com'])->andReturn(false);
        $this->schoolRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($school);
        $this->userRepository->shouldReceive('create')->andReturn($user);
        $this->userRepository->shouldReceive('update')->with($user);
        $this->authenticationRepository->shouldReceive('create')->andReturn($authentication);
        $this->authenticationRepository->shouldReceive('update')->with($authentication);
        $this->sessionUserProvider->shouldReceive('createSessionUserFromUser')->with($user)->andReturn($sessionUser);
    }

    protected function checkOuput()
    {
        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/abc\s+\| first\s+\| last\s+\| email@example.com\s+\| abc123\s+\| phone\s+\| yes/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Success! New user #1 Test Person created./',
            $output
        );
    }
}
