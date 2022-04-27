<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\SyncAllUsersCommand;
use App\Repository\AuthenticationRepository;
use App\Repository\PendingUserUpdateRepository;
use App\Repository\UserRepository;
use App\Service\Directory;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\AuthenticationInterface;
use App\Entity\UserInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class SyncAllUsersCommandTest
 * @group cli
 */
class SyncAllUsersCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private const COMMAND_NAME = 'ilios:sync-users';

    /**
     * @var m\Mock
     */
    protected $userRepository;

    /**
     * @var m\Mock
     */
    protected $authenticationRepository;

    /**
     * @var m\Mock
     */
    protected $pendingUserUpdateRepository;
    protected $commandTester;
    protected $questionHelper;

    /**
     * @var m\Mock
     */
    protected $directory;

    /**
     * @var m\Mock
     */
    protected $em;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = m::mock(UserRepository::class);
        $this->authenticationRepository = m::mock(AuthenticationRepository::class);
        $this->pendingUserUpdateRepository = m::mock(PendingUserUpdateRepository::class);
        $this->directory = m::mock(Directory::class);
        $this->em = m::mock(EntityManagerInterface::class);

        $command = new SyncAllUsersCommand(
            $this->userRepository,
            $this->authenticationRepository,
            $this->pendingUserUpdateRepository,
            $this->directory,
            $this->em
        );
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
        $this->questionHelper = $command->getHelper('question');

        $this->pendingUserUpdateRepository->shouldReceive('removeAllPendingUserUpdates')->once();
        $this->userRepository->shouldReceive('resetExaminedFlagForAllUsers')->once();
    }

    /**
     * Remove all mock objects
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->userRepository);
        unset($this->authenticationRepository);
        unset($this->pendingUserUpdateRepository);
        unset($this->directory);
        unset($this->em);
        unset($this->commandTester);
    }

    public function testExecuteUserWithNoChanges()
    {
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'displayName' => 'display',
            'pronouns' => 'pronouns',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'username',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $authentication = m::mock(AuthenticationInterface::class)
            ->shouldReceive('getUsername')->andReturn('username')
            ->mock();
        $user = m::mock(UserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('first last')
            ->shouldReceive('getFirstName')->andReturn('first')
            ->shouldReceive('getLastName')->andReturn('last')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getDisplayName')->andReturn('display')
            ->shouldReceive('getPronouns')->andReturn('pronouns')
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false])
            ->andReturn([$user])
            ->once();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())
            ->andReturn([])
            ->once();
        $this->userRepository->shouldReceive('update')->with($user, false)->once();
        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Comparing User #42 first last \(email\) to directory user by campus ID abc./',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Completed sync process 1 users found in the directory; 0 users updated./',
            $output
        );
    }

    public function testExecuteWithFirstNameChange()
    {
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'new-first',
            'lastName' => 'last',
            'email' => 'email',
            'displayName' => 'display',
            'pronouns' => 'pronouns',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'abc123',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $authentication = m::mock(AuthenticationInterface::class)
            ->shouldReceive('getUsername')->andReturn('abc123')
            ->mock();
        $user = m::mock(UserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('first last')
            ->shouldReceive('getFirstName')->andReturn('first')
            ->shouldReceive('getLastName')->andReturn('last')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getDisplayName')->andReturn('display')
            ->shouldReceive('getPronouns')->andReturn('pronouns')
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('setExamined')->with(true)
            ->shouldReceive('setFirstName')->with('new-first')
            ->mock();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false])
            ->andReturn([$user])
            ->once();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userRepository->shouldReceive('update')->with($user, false)->once();

        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Updating first name from "first" to "new-first"./',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Completed sync process 1 users found in the directory; 1 users updated./',
            $output
        );
    }

    public function testExecuteWithLastNameChange()
    {
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'new-last',
            'email' => 'email',
            'displayName' => 'display',
            'pronouns' => 'pronouns',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'abc123',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $authentication = m::mock(AuthenticationInterface::class)
            ->shouldReceive('getUsername')->andReturn('abc123')
            ->mock();
        $user = m::mock(UserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('first last')
            ->shouldReceive('getFirstName')->andReturn('first')
            ->shouldReceive('getLastName')->andReturn('last')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getDisplayName')->andReturn('display')
            ->shouldReceive('getPronouns')->andReturn('pronouns')
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('setExamined')->with(true)
            ->shouldReceive('setLastName')->with('new-last')
            ->mock();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false])
            ->andReturn([$user])
            ->once();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userRepository->shouldReceive('update')->with($user, false)->once();

        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Updating last name from "last" to "new-last"./',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Completed sync process 1 users found in the directory; 1 users updated./',
            $output
        );
    }

    public function testExecuteWithPhoneNumberChange()
    {
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'displayName' => 'display',
            'pronouns' => 'pronouns',
            'telephoneNumber' => 'new-phone',
            'campusId' => 'abc',
            'username' => 'abc123',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $authentication = m::mock(AuthenticationInterface::class)
            ->shouldReceive('getUsername')->andReturn('abc123')
            ->mock();
        $user = m::mock(UserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('first last')
            ->shouldReceive('getFirstName')->andReturn('first')
            ->shouldReceive('getLastName')->andReturn('last')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getDisplayName')->andReturn('display')
            ->shouldReceive('getPronouns')->andReturn('pronouns')
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('setExamined')->with(true)
            ->shouldReceive('setPhone')->with('new-phone')
            ->mock();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false])
            ->andReturn([$user])
            ->once();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userRepository->shouldReceive('update')->with($user, false)->once();

        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Updating phone number from "phone" to "new-phone"./',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Completed sync process 1 users found in the directory; 1 users updated./',
            $output
        );
    }

    public function testExecuteWithEmailChange()
    {
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'new-email',
            'displayName' => 'display',
            'pronouns' => 'pronouns',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'abc123',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $authentication = m::mock(AuthenticationInterface::class)
            ->shouldReceive('getUsername')->andReturn('abc123')
            ->mock();
        $user = m::mock(UserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('first last')
            ->shouldReceive('getFirstName')->andReturn('first')
            ->shouldReceive('getLastName')->andReturn('last')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getDisplayName')->andReturn('display')
            ->shouldReceive('getPronouns')->andReturn('pronouns')
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false])
            ->andReturn([$user])
            ->once();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userRepository->shouldReceive('update')->with($user, false)->once();
        $update = m::mock('App\Entity\PendingUserUpdate')
            ->shouldReceive('setType')->with('emailMismatch')->once()
            ->shouldReceive('setProperty')->with('email')->once()
            ->shouldReceive('setValue')->with('new-email')->once()
            ->shouldReceive('setUser')->with($user)->once()
            ->mock();
        $this->pendingUserUpdateRepository->shouldReceive('create')->andReturn($update)->once();
        $this->pendingUserUpdateRepository->shouldReceive('update')->with($update, false)->once();

        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Email address "email" differs from "new-email" logging for further action./',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Completed sync process 1 users found in the directory; 0 users updated./',
            $output
        );
    }

    public function testExecuteWithEmailCaseChange()
    {
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'EMAIL',
            'displayName' => 'display',
            'pronouns' => 'pronouns',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'abc123',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $authentication = m::mock(AuthenticationInterface::class)
            ->shouldReceive('getUsername')->andReturn('abc123')
            ->mock();
        $user = m::mock(UserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('first last')
            ->shouldReceive('getFirstName')->andReturn('first')
            ->shouldReceive('getLastName')->andReturn('last')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getDisplayName')->andReturn('display')
            ->shouldReceive('getPronouns')->andReturn('pronouns')
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('setExamined')->with(true)
            ->shouldReceive('setEmail')->with('EMAIL')
            ->mock();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false])
            ->andReturn([$user])
            ->once();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userRepository->shouldReceive('update')->with($user, false)->once();

        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Updating email from "email" to "EMAIL" since the only difference was the case./',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Completed sync process 1 users found in the directory; 1 users updated./',
            $output
        );
    }

    public function testExecuteWithDisplayNameChange()
    {
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'displayName' => 'new display',
            'pronouns' => 'pronouns',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'abc123',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $authentication = m::mock(AuthenticationInterface::class)
            ->shouldReceive('getUsername')->andReturn('abc123')
            ->mock();
        $user = m::mock(UserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('first last')
            ->shouldReceive('getFirstName')->andReturn('first')
            ->shouldReceive('getLastName')->andReturn('last')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getDisplayName')->andReturn('display')
            ->shouldReceive('getPronouns')->andReturn('pronouns')
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('setExamined')->with(true)
            ->shouldReceive('setDisplayName')->with('new display')
            ->mock();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false])
            ->andReturn([$user])
            ->once();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userRepository->shouldReceive('update')->with($user, false)->once();

        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Updating display name from "display" to "new display"./',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Completed sync process 1 users found in the directory; 1 users updated./',
            $output
        );
    }

    public function testExecuteWithPronounChange()
    {
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'displayName' => 'display',
            'pronouns' => 'new pronouns',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'abc123',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $authentication = m::mock(AuthenticationInterface::class)
            ->shouldReceive('getUsername')->andReturn('abc123')
            ->mock();
        $user = m::mock(UserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('first last')
            ->shouldReceive('getFirstName')->andReturn('first')
            ->shouldReceive('getLastName')->andReturn('last')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getDisplayName')->andReturn('display')
            ->shouldReceive('getPronouns')->andReturn('pronouns')
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('setExamined')->with(true)
            ->shouldReceive('setPronouns')->with('new pronouns')
            ->mock();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false])
            ->andReturn([$user])
            ->once();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userRepository->shouldReceive('update')->with($user, false)->once();

        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Updating pronouns from "pronouns" to "new pronouns"./',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Completed sync process 1 users found in the directory; 1 users updated./',
            $output
        );
    }

    public function testExecuteWithPronounRemoved()
    {
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'displayName' => 'display',
            'pronouns' => null,
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'abc123',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $authentication = m::mock(AuthenticationInterface::class)
            ->shouldReceive('getUsername')->andReturn('abc123')
            ->mock();
        $user = m::mock(UserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('first last')
            ->shouldReceive('getFirstName')->andReturn('first')
            ->shouldReceive('getLastName')->andReturn('last')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getDisplayName')->andReturn('display')
            ->shouldReceive('getPronouns')->andReturn('pronouns')
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('setExamined')->with(true)
            ->shouldReceive('setPronouns')->with(null)
            ->mock();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false])
            ->andReturn([$user])
            ->once();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userRepository->shouldReceive('update')->with($user, false)->once();

        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Removing "pronouns" pronoun./',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Completed sync process 1 users found in the directory; 1 users updated./',
            $output
        );
    }

    public function testExecuteWithUsernameChange()
    {
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'displayName' => 'display',
            'pronouns' => 'pronouns',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'new-abc123',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $authentication = m::mock(AuthenticationInterface::class)
            ->shouldReceive('getUsername')->andReturn('abc123')
            ->shouldReceive('setUsername')->with('new-abc123')
            ->mock();
        $user = m::mock(UserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('first last')
            ->shouldReceive('getFirstName')->andReturn('first')
            ->shouldReceive('getLastName')->andReturn('last')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getDisplayName')->andReturn('display')
            ->shouldReceive('getPronouns')->andReturn('pronouns')
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false])
            ->andReturn([$user])
            ->once();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userRepository->shouldReceive('update')->with($user, false)->once();

        $this->authenticationRepository->shouldReceive('update')->with($authentication, false)->once();
        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Updating username from "abc123" to "new-abc123"./',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Completed sync process 1 users found in the directory; 1 users updated./',
            $output
        );
    }


    public function testExecuteWithNoAuthenticationDataChange()
    {
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'displayName' => 'display',
            'pronouns' => 'pronouns',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'abc123',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $user = m::mock(UserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('first last')
            ->shouldReceive('getFirstName')->andReturn('first')
            ->shouldReceive('getLastName')->andReturn('last')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getDisplayName')->andReturn('display')
            ->shouldReceive('getPronouns')->andReturn('pronouns')
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn(null)
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $authentication = m::mock(AuthenticationInterface::class)
            ->shouldReceive('setUser')->with($user)
            ->shouldReceive('setUsername')->with('abc123')
            ->shouldReceive('getUsername')->andReturn('')
            ->mock();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false])
            ->andReturn([$user])
            ->once();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userRepository->shouldReceive('update')->with($user, false)->once();

        $this->authenticationRepository->shouldReceive('findOneBy')
            ->with(['username' => 'abc123'])->once()->andReturn(false);
        $this->authenticationRepository->shouldReceive('create')->andReturn($authentication)->once();
        $this->authenticationRepository->shouldReceive('update')->with($authentication, false)->once();
        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Updating username from "" to "abc123"./',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/User had no authentication data, creating it now./',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Completed sync process 1 users found in the directory; 1 users updated./',
            $output
        );
    }

    public function testExecuteWithMultipleUserMatches()
    {
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'displayName' => 'display',
            'pronouns' => 'pronouns',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'abc123',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $user1 = m::mock(UserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('first last')
            ->shouldReceive('getFirstName')->andReturn('first')
            ->shouldReceive('getLastName')->andReturn('last')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getDisplayName')->andReturn('display')
            ->shouldReceive('getPronouns')->andReturn('pronouns')
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn(null)
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $user2 = m::mock(UserInterface::class)
            ->shouldReceive('getId')->andReturn(11)
            ->shouldReceive('getFirstAndLastName')->andReturn('other guy')
            ->shouldReceive('getFirstName')->andReturn('other')
            ->shouldReceive('getLastName')->andReturn('guy')
            ->shouldReceive('getEmail')->andReturn('other-guy')
            ->shouldReceive('getPhone')->andReturn('')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn(null)
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false])
            ->andReturn([$user1, $user2])
            ->once();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userRepository->shouldReceive('update')->with($user1, false)->once();
        $this->userRepository->shouldReceive('update')->with($user2, false)->once();

        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Multiple accounts exist for the same campus ID \(abc\)\.  None of them will be updated./',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Completed sync process 1 users found in the directory; 0 users updated./',
            $output
        );
    }

    public function testExecuteWithEmptyFirstName()
    {
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => '',
            'lastName' => 'last',
            'email' => 'email',
            'displayName' => 'display',
            'pronouns' => 'pronouns',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'abc123',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $user = m::mock(UserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('missing person')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getDisplayName')->andReturn('display')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false])
            ->andReturn([$user])
            ->once();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userRepository->shouldReceive('update')->with($user, false)->once();

        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/firstName is required and it is missing from record with campus ID \(abc\)\.  User will not be updated./',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Completed sync process 1 users found in the directory; 0 users updated./',
            $output
        );
    }

    public function testExecuteWithEmptyLastName()
    {
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => '',
            'email' => 'email',
            'displayName' => 'display',
            'pronouns' => 'pronouns',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'abc123',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $user = m::mock(UserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('missing person')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getDisplayName')->andReturn('display')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false])
            ->andReturn([$user])
            ->once();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userRepository->shouldReceive('update')->with($user, false)->once();

        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/lastName is required and it is missing from record with campus ID \(abc\)\.  User will not be updated./',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Completed sync process 1 users found in the directory; 0 users updated./',
            $output
        );
    }

    public function testExecuteWithEmptyEmail()
    {
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => '',
            'displayName' => 'display',
            'pronouns' => 'pronouns',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'abc123',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $user = m::mock(UserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('missing person')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getDisplayName')->andReturn('display')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false])
            ->andReturn([$user])
            ->once();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userRepository->shouldReceive('update')->with($user, false)->once();

        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/email is required and it is missing from record with campus ID \(abc\)\.  User will not be updated./',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Completed sync process 1 users found in the directory; 0 users updated./',
            $output
        );
    }

    public function testExecuteWithEmptyUsernamelName()
    {
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'displayName' => 'display',
            'pronouns' => 'pronouns',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => '',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $user = m::mock(UserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('missing person')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getDisplayName')->andReturn('display')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false])
            ->andReturn([$user])
            ->once();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userRepository->shouldReceive('update')->with($user, false)->once();

        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/username is required and it is missing from record with campus ID \(abc\)\.  User will not be updated./',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Completed sync process 1 users found in the directory; 0 users updated./',
            $output
        );
    }

    public function testExecuteWithUserNotInTheDirectory()
    {
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'displayName' => 'display',
            'pronouns' => 'pronouns',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'abc123',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([]);
        $user = m::mock(UserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('missing person')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getDisplayName')->andReturn('display')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->mock();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([$user])
            ->once();

        $update = m::mock('App\Entity\PendingUserUpdate')
            ->shouldReceive('setType')->with('missingFromDirectory')->once()
            ->shouldReceive('setUser')->with($user)->once()
            ->mock();
        $this->pendingUserUpdateRepository->shouldReceive('create')->andReturn($update)->once();
        $this->pendingUserUpdateRepository->shouldReceive('update')->with($update, false)->once();

        $this->em->shouldReceive('flush')->once();
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/User #42 missing person email abc not found in the directory.  Logged for further study/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Completed sync process 0 users found in the directory; 0 users updated./',
            $output
        );
    }

    public function testExecuteEmailChangeDoesNotChangeOthers()
    {
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'new-first',
            'lastName' => 'new-last',
            'email' => 'new-email',
            'displayName' => 'new-display',
            'telephoneNumber' => 'new-phone',
            'campusId' => 'abc',
            'username' => 'new-abc123',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $authentication = m::mock(AuthenticationInterface::class)
            ->shouldReceive('getUsername')->andReturn('abc123')
            ->mock();
        $user = m::mock(UserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('first last')
            ->shouldReceive('getFirstName')->andReturn('first')
            ->shouldReceive('getLastName')->andReturn('last')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getDisplayName')->andReturn('display')
            ->shouldReceive('getPronouns')->andReturn('pronouns')
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false])
            ->andReturn([$user])
            ->once();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userRepository->shouldReceive('update')->with($user, false)->once();
        $update = m::mock('App\Entity\PendingUserUpdate')
            ->shouldReceive('setType')->with('emailMismatch')->once()
            ->shouldReceive('setProperty')->with('email')->once()
            ->shouldReceive('setValue')->with('new-email')->once()
            ->shouldReceive('setUser')->with($user)->once()
            ->mock();
        $this->pendingUserUpdateRepository->shouldReceive('create')->andReturn($update)->once();
        $this->pendingUserUpdateRepository->shouldReceive('update')->with($update, false)->once();

        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Completed sync process 1 users found in the directory; 0 users updated./',
            $output
        );
    }

    public function testExecuteWithIgnoredAuthenticationWithSameUserName()
    {
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'displayName' => 'display',
            'pronouns' => 'pronouns',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'abc123',
        ];
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([$fakeDirectoryUser]);
        $user1 = m::mock(UserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstAndLastName')->andReturn('first last')
            ->shouldReceive('getFirstName')->andReturn('first')
            ->shouldReceive('getLastName')->andReturn('last')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getDisplayName')->andReturn('display')
            ->shouldReceive('getPronouns')->andReturn('pronouns')
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn(null)
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $user2 = m::mock(UserInterface::class)
            ->shouldReceive('getId')->andReturn(11)
            ->shouldReceive('getFirstAndLastName')->andReturn('other guy')
            ->shouldReceive('getFirstName')->andReturn('other')
            ->shouldReceive('getLastName')->andReturn('guy')
            ->shouldReceive('getEmail')->andReturn('other-guy')
            ->shouldReceive('getPhone')->andReturn('')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn(null)
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $duplicateAuthentication = m::mock(AuthenticationInterface::class)
            ->shouldReceive('getUser')->andReturn($user2)
            ->mock();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false])
            ->andReturn([$user1])
            ->once();
        $this->userRepository->shouldReceive('update')->with($user1, false)->once();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())
            ->andReturn([])
            ->once();
        $this->authenticationRepository->shouldReceive('findOneBy')
            ->with(['username' => 'abc123'])->once()->andReturn($duplicateAuthentication);

        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/\[E\] There is already an account for username abc123 belonging to user #11 with Campus ID abc/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Completed sync process 1 users found in the directory; 0 users updated./',
            $output
        );
    }
}
