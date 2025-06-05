<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\Attributes\Group;
use App\Classes\SessionUserInterface;
use App\Entity\UserInterface;
use App\Repository\AuthenticationRepository;
use App\Repository\SchoolRepository;
use App\Repository\UserRepository;
use App\Service\SessionUserProvider;
use App\Command\AddUserCommand;
use App\Entity\AuthenticationInterface;
use App\Entity\SchoolInterface;
use App\Tests\Helper\TestQuestionHelper;
use Exception;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class AddUserCommandTest
 */
#[Group('cli')]
final class AddUserCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected m\MockInterface $userRepository;
    protected m\MockInterface $authenticationRepository;
    protected m\MockInterface $schoolRepository;
    protected m\MockInterface $hasher;
    protected CommandTester $commandTester;
    protected HelperInterface $questionHelper;
    protected m\MockInterface $sessionUserProvider;

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
        $commandInApp = $application->find($command->getName());

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

    public function testExecute(): void
    {
        $this->getReadyForInput();
        $this->commandTester->setInputs(['Yes']);

        $this->commandTester->execute(
            [
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

        $this->checkOutput();
    }


    public function testBadSchoolId(): void
    {
        $this->userRepository->shouldReceive('findOneBy')->with(['campusId' => 1])->andReturn(null);
        $this->schoolRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn(null);
        $this->expectException(Exception::class);
        $this->commandTester->execute(
            [
                '--schoolId' => '1',
            ]
        );
    }

    public function testAskForMissingFirstName(): void
    {
        $this->getReadyForInput();

        $this->commandTester->setInputs(['first', 'Yes']);

        $this->commandTester->execute(
            [
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

        $this->checkOutput();
    }

    public function testAskForMissingLastName(): void
    {
        $this->getReadyForInput();

        $this->commandTester->setInputs(['last', 'Yes']);

        $this->commandTester->execute(
            [
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

        $this->checkOutput();
    }

    public function testAskForMissingEmail(): void
    {
        $this->getReadyForInput();

        $this->commandTester->setInputs(['email@example.com', 'Yes']);

        $this->commandTester->execute(
            [
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

        $this->checkOutput();
    }

    public function testAskIfIsRoot(): void
    {
        $this->getReadyForInput();

        $this->commandTester->setInputs(['Yes', 'Yes']);

        $this->commandTester->execute(
            [
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

        $this->checkOutput();
    }

    public function testAskForMissingPhone(): void
    {
        $this->getReadyForInput();

        $this->commandTester->setInputs(['phone', 'Yes']);

        $this->commandTester->execute(
            [
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

        $this->checkOutput();
    }

    public function testAskForMissingCampusId(): void
    {
        $this->getReadyForInput();

        $this->commandTester->setInputs(['abc', 'Yes']);

        $this->commandTester->execute(
            [
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

        $this->checkOutput();
    }

    public function testAskForMissingUsername(): void
    {
        $this->getReadyForInput();

        $this->commandTester->setInputs(['abc123', 'Yes']);

        $this->commandTester->execute(
            [
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

        $this->checkOutput();
    }

    public function testAskForMissingPassword(): void
    {
        $this->getReadyForInput();

        $this->commandTester->setInputs(['abc123pass', 'Yes']);

        $this->commandTester->execute(
            [
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

        $this->checkOutput();
    }

    protected function getReadyForInput(): void
    {
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getTitle')->andReturn('Big School Title');
        $sessionUser = m::mock(SessionUserInterface::class);
        $user = m::mock(UserInterface::class);
        $user->shouldReceive('setFirstName')->with('first');
        $user->shouldReceive('setLastName')->with('last');
        $user->shouldReceive('setEmail')->with('email@example.com');
        $user->shouldReceive('setPhone')->with('phone');
        $user->shouldReceive('setCampusId')->with('abc');
        $user->shouldReceive('setAddedViaIlios')->with(true);
        $user->shouldReceive('setEnabled')->with(true);
        $user->shouldReceive('setUserSyncIgnore')->with(false);
        $user->shouldReceive('setSchool')->with($school);
        $user->shouldReceive('getId')->andReturn(1);
        $user->shouldReceive('getFirstAndLastName')->andReturn('Test Person');
        $user->shouldReceive('setRoot')->with(true);

        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('setUsername')->with('abc123');
        $authentication->shouldReceive('setPasswordHash')->with('hashBlurb');
        $authentication->shouldReceive('getUser')->andReturn($user);

        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('setAuthentication')->with($authentication);

        $this->hasher->shouldReceive('hashPassword')->with($sessionUser, 'abc123pass')->andReturn('hashBlurb');

        $this->userRepository->shouldReceive('findOneBy')->with(['campusId' => 'abc'])->andReturn(null);
        $this->userRepository->shouldReceive('findOneBy')->with(['email' => 'email@example.com'])->andReturn(null);
        $this->schoolRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($school);
        $this->userRepository->shouldReceive('create')->andReturn($user);
        $this->userRepository->shouldReceive('update')->with($user);
        $this->authenticationRepository->shouldReceive('create')->andReturn($authentication);
        $this->authenticationRepository->shouldReceive('update')->with($authentication);
        $this->sessionUserProvider->shouldReceive('createSessionUserFromUser')->with($user)->andReturn($sessionUser);
    }

    protected function checkOutput(): void
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
