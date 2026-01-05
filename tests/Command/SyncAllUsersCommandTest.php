<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\Attributes\Group;
use App\Command\SyncAllUsersCommand;
use App\Entity\PendingUserUpdateInterface;
use App\Repository\AuthenticationRepository;
use App\Repository\PendingUserUpdateRepository;
use App\Repository\UserRepository;
use App\Service\Directory;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\AuthenticationInterface;
use App\Entity\UserInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class SyncAllUsersCommandTest
 */
#[Group('cli')]
final class SyncAllUsersCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected m\MockInterface $userRepository;
    protected m\MockInterface $authenticationRepository;
    protected m\MockInterface $pendingUserUpdateRepository;
    protected CommandTester $commandTester;
    protected m\MockInterface $directory;
    protected m\MockInterface $em;

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
        $application->addCommands([$command]);
        $commandInApp = $application->find($command->getName());
        $this->commandTester = new CommandTester($commandInApp);
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

    public function testExecuteUserWithNoChanges(): void
    {
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'middleName' => 'middle',
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
        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('getUsername')->andReturn('username');

        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn(42);
        $user->shouldReceive('getFirstAndLastName')->andReturn('first last');
        $user->shouldReceive('getFirstName')->andReturn('first');
        $user->shouldReceive('getMiddleName')->andReturn('middle');
        $user->shouldReceive('getLastName')->andReturn('last');
        $user->shouldReceive('getEmail')->andReturn('email');
        $user->shouldReceive('getDisplayName')->andReturn('display');
        $user->shouldReceive('getPronouns')->andReturn('pronouns');
        $user->shouldReceive('getPhone')->andReturn('phone');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('setExamined')->with(true);

        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true])
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
        $this->commandTester->execute([]);

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

    public function testExecuteWithFirstNameChange(): void
    {
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'new-first',
            'middleName' => 'middle',
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
        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('getUsername')->andReturn('abc123');

        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn(42);
        $user->shouldReceive('getFirstAndLastName')->andReturn('first last');
        $user->shouldReceive('getFirstName')->andReturn('first');
        $user->shouldReceive('getMiddleName')->andReturn('middle');
        $user->shouldReceive('getLastName')->andReturn('last');
        $user->shouldReceive('getEmail')->andReturn('email');
        $user->shouldReceive('getDisplayName')->andReturn('display');
        $user->shouldReceive('getPronouns')->andReturn('pronouns');
        $user->shouldReceive('getPhone')->andReturn('phone');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('setExamined')->with(true);
        $user->shouldReceive('setFirstName')->with('new-first');

        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true])
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
        $this->commandTester->execute([]);

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

    public function testExecuteWithPreferredFirstNameChange(): void
    {
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'new legal first',
            'preferredFirstName' => 'new-first',
            'middleName' => 'middle',
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
        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('getUsername')->andReturn('abc123');

        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn(42);
        $user->shouldReceive('getFirstAndLastName')->andReturn('first last');
        $user->shouldReceive('getFirstName')->andReturn('first');
        $user->shouldReceive('getMiddleName')->andReturn('middle');
        $user->shouldReceive('getLastName')->andReturn('last');
        $user->shouldReceive('getEmail')->andReturn('email');
        $user->shouldReceive('getDisplayName')->andReturn('display');
        $user->shouldReceive('getPronouns')->andReturn('pronouns');
        $user->shouldReceive('getPhone')->andReturn('phone');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('setExamined')->with(true);
        $user->shouldReceive('setFirstName')->with('new-first');

        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true])
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
        $this->commandTester->execute([]);

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

    public function testExecuteWithNoMiddleNameChange(): void
    {
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'middleName' => 'middle',
            'preferredMiddleName' => '',
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
        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('getUsername')->andReturn('abc123');

        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn(42);
        $user->shouldReceive('getFirstAndLastName')->andReturn('first last');
        $user->shouldReceive('getFirstName')->andReturn('first');
        $user->shouldReceive('getMiddleName')->andReturn('');
        $user->shouldReceive('getLastName')->andReturn('last');
        $user->shouldReceive('getEmail')->andReturn('email');
        $user->shouldReceive('getDisplayName')->andReturn('display');
        $user->shouldReceive('getPronouns')->andReturn('pronouns');
        $user->shouldReceive('getPhone')->andReturn('phone');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('setExamined')->with(true);

        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true])
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
        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Completed sync process 1 users found in the directory; 0 users updated./',
            $output
        );
    }

    public function testExecuteWithMiddleNameChange(): void
    {
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'middleName' => 'new-middle',
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
        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('getUsername')->andReturn('abc123');

        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn(42);
        $user->shouldReceive('getFirstAndLastName')->andReturn('first last');
        $user->shouldReceive('getFirstName')->andReturn('first');
        $user->shouldReceive('getMiddleName')->andReturn('middle');
        $user->shouldReceive('getLastName')->andReturn('last');
        $user->shouldReceive('getEmail')->andReturn('email');
        $user->shouldReceive('getDisplayName')->andReturn('display');
        $user->shouldReceive('getPronouns')->andReturn('pronouns');
        $user->shouldReceive('getPhone')->andReturn('phone');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('setExamined')->with(true);
        $user->shouldReceive('setMiddleName')->with('new-middle');

        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true])
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
        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Updating middle name from "middle" to "new-middle"./',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Completed sync process 1 users found in the directory; 1 users updated./',
            $output
        );
    }

    public function testExecuteWithPreferredMiddleNameChange(): void
    {
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'middleName' => 'middle',
            'lastName' => 'last',
            'preferredMiddleName' => 'new-middle',
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
        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('getUsername')->andReturn('abc123');

        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn(42);
        $user->shouldReceive('getFirstAndLastName')->andReturn('first last');
        $user->shouldReceive('getFirstName')->andReturn('first');
        $user->shouldReceive('getMiddleName')->andReturn('middle');
        $user->shouldReceive('getLastName')->andReturn('last');
        $user->shouldReceive('getEmail')->andReturn('email');
        $user->shouldReceive('getDisplayName')->andReturn('display');
        $user->shouldReceive('getPronouns')->andReturn('pronouns');
        $user->shouldReceive('getPhone')->andReturn('phone');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('setExamined')->with(true);
        $user->shouldReceive('setMiddleName')->with('new-middle');

        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true])
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
        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Updating middle name from "middle" to "new-middle"./',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Completed sync process 1 users found in the directory; 1 users updated./',
            $output
        );
    }

    public function testExecuteWitMiddleNameNotInResults(): void
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
        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('getUsername')->andReturn('abc123');

        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn(42);
        $user->shouldReceive('getFirstAndLastName')->andReturn('first last');
        $user->shouldReceive('getFirstName')->andReturn('first');
        $user->shouldReceive('getMiddleName')->andReturn('middle');
        $user->shouldReceive('getLastName')->andReturn('last');
        $user->shouldReceive('getEmail')->andReturn('email');
        $user->shouldReceive('getDisplayName')->andReturn('display');
        $user->shouldReceive('getPronouns')->andReturn('pronouns');
        $user->shouldReceive('getPhone')->andReturn('phone');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('setExamined')->with(true);
        $user->shouldReceive('setMiddleName')->with(null);

        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true])
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
        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Updating middle name from "middle" to ""./',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Completed sync process 1 users found in the directory; 1 users updated./',
            $output
        );
    }

    public function testExecuteWithLastNameChange(): void
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
        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('getUsername')->andReturn('abc123');

        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn(42);
        $user->shouldReceive('getFirstAndLastName')->andReturn('first last');
        $user->shouldReceive('getFirstName')->andReturn('first');
        $user->shouldReceive('getMiddleName')->andReturn(null);
        $user->shouldReceive('getLastName')->andReturn('last');
        $user->shouldReceive('getEmail')->andReturn('email');
        $user->shouldReceive('getDisplayName')->andReturn('display');
        $user->shouldReceive('getPronouns')->andReturn('pronouns');
        $user->shouldReceive('getPhone')->andReturn('phone');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('setExamined')->with(true);
        $user->shouldReceive('setLastName')->with('new-last');

        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true])
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
        $this->commandTester->execute([]);

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

    public function testExecuteWithPreferredLastNameChange(): void
    {
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'new legal last',
            'preferredLastName' => 'new-last',
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
        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('getUsername')->andReturn('abc123');

        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn(42);
        $user->shouldReceive('getFirstAndLastName')->andReturn('first last');
        $user->shouldReceive('getFirstName')->andReturn('first');
        $user->shouldReceive('getMiddleName')->andReturn(null);
        $user->shouldReceive('getLastName')->andReturn('last');
        $user->shouldReceive('getEmail')->andReturn('email');
        $user->shouldReceive('getDisplayName')->andReturn('display');
        $user->shouldReceive('getPronouns')->andReturn('pronouns');
        $user->shouldReceive('getPhone')->andReturn('phone');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('setExamined')->with(true);
        $user->shouldReceive('setLastName')->with('new-last');

        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true])
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
        $this->commandTester->execute([]);

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

    public function testExecuteWithPhoneNumberChange(): void
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
        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('getUsername')->andReturn('abc123');

        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn(42);
        $user->shouldReceive('getFirstAndLastName')->andReturn('first last');
        $user->shouldReceive('getFirstName')->andReturn('first');
        $user->shouldReceive('getMiddleName')->andReturn(null);
        $user->shouldReceive('getLastName')->andReturn('last');
        $user->shouldReceive('getEmail')->andReturn('email');
        $user->shouldReceive('getDisplayName')->andReturn('display');
        $user->shouldReceive('getPronouns')->andReturn('pronouns');
        $user->shouldReceive('getPhone')->andReturn('phone');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('setExamined')->with(true);
        $user->shouldReceive('setPhone')->with('new-phone');

        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true])
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
        $this->commandTester->execute([]);

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

    public function testExecuteWithEmailChange(): void
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
        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('getUsername')->andReturn('abc123');

        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn(42);
        $user->shouldReceive('getFirstAndLastName')->andReturn('first last');
        $user->shouldReceive('getFirstName')->andReturn('first');
        $user->shouldReceive('getMiddleName')->andReturn(null);
        $user->shouldReceive('getLastName')->andReturn('last');
        $user->shouldReceive('getEmail')->andReturn('email');
        $user->shouldReceive('getDisplayName')->andReturn('display');
        $user->shouldReceive('getPronouns')->andReturn('pronouns');
        $user->shouldReceive('getPhone')->andReturn('phone');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('setExamined')->with(true);

        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true])
            ->andReturn([$user])
            ->once();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userRepository->shouldReceive('update')->with($user, false)->once();
        $update = m::mock(PendingUserUpdateInterface::class);
        $update->shouldReceive('setType')->with('emailMismatch')->once();
        $update->shouldReceive('setProperty')->with('email')->once();
        $update->shouldReceive('setValue')->with('new-email')->once();
        $update->shouldReceive('setUser')->with($user)->once();

        $this->pendingUserUpdateRepository->shouldReceive('create')->andReturn($update)->once();
        $this->pendingUserUpdateRepository->shouldReceive('update')->with($update, false)->once();

        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute([]);

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

    public function testExecuteWithEmailCaseChange(): void
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
        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('getUsername')->andReturn('abc123');

        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn(42);
        $user->shouldReceive('getFirstAndLastName')->andReturn('first last');
        $user->shouldReceive('getFirstName')->andReturn('first');
        $user->shouldReceive('getMiddleName')->andReturn(null);
        $user->shouldReceive('getLastName')->andReturn('last');
        $user->shouldReceive('getEmail')->andReturn('email');
        $user->shouldReceive('getDisplayName')->andReturn('display');
        $user->shouldReceive('getPronouns')->andReturn('pronouns');
        $user->shouldReceive('getPhone')->andReturn('phone');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('setExamined')->with(true);
        $user->shouldReceive('setEmail')->with('EMAIL');

        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true])
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
        $this->commandTester->execute([]);

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

    public function testExecuteWithDisplayNameChange(): void
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
        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('getUsername')->andReturn('abc123');

        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn(42);
        $user->shouldReceive('getFirstAndLastName')->andReturn('first last');
        $user->shouldReceive('getFirstName')->andReturn('first');
        $user->shouldReceive('getMiddleName')->andReturn(null);
        $user->shouldReceive('getLastName')->andReturn('last');
        $user->shouldReceive('getEmail')->andReturn('email');
        $user->shouldReceive('getDisplayName')->andReturn('display');
        $user->shouldReceive('getPronouns')->andReturn('pronouns');
        $user->shouldReceive('getPhone')->andReturn('phone');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('setExamined')->with(true);
        $user->shouldReceive('setDisplayName')->with('new display');

        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true])
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
        $this->commandTester->execute([]);

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

    public function testExecuteWithPronounChange(): void
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
        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('getUsername')->andReturn('abc123');

        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn(42);
        $user->shouldReceive('getFirstAndLastName')->andReturn('first last');
        $user->shouldReceive('getFirstName')->andReturn('first');
        $user->shouldReceive('getMiddleName')->andReturn(null);
        $user->shouldReceive('getLastName')->andReturn('last');
        $user->shouldReceive('getEmail')->andReturn('email');
        $user->shouldReceive('getDisplayName')->andReturn('display');
        $user->shouldReceive('getPronouns')->andReturn('pronouns');
        $user->shouldReceive('getPhone')->andReturn('phone');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('setExamined')->with(true);
        $user->shouldReceive('setPronouns')->with('new pronouns');

        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true])
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
        $this->commandTester->execute([]);

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

    public function testExecuteWithPronounRemoved(): void
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
        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('getUsername')->andReturn('abc123');

        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn(42);
        $user->shouldReceive('getFirstAndLastName')->andReturn('first last');
        $user->shouldReceive('getFirstName')->andReturn('first');
        $user->shouldReceive('getMiddleName')->andReturn(null);
        $user->shouldReceive('getLastName')->andReturn('last');
        $user->shouldReceive('getEmail')->andReturn('email');
        $user->shouldReceive('getDisplayName')->andReturn('display');
        $user->shouldReceive('getPronouns')->andReturn('pronouns');
        $user->shouldReceive('getPhone')->andReturn('phone');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('setExamined')->with(true);
        $user->shouldReceive('setPronouns')->with(null);

        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true])
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
        $this->commandTester->execute([]);

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

    public function testExecuteWithUsernameChange(): void
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
        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('getUsername')->andReturn('abc123');
        $authentication->shouldReceive('setUsername')->with('new-abc123');

        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn(42);
        $user->shouldReceive('getFirstAndLastName')->andReturn('first last');
        $user->shouldReceive('getFirstName')->andReturn('first');
        $user->shouldReceive('getMiddleName')->andReturn(null);
        $user->shouldReceive('getLastName')->andReturn('last');
        $user->shouldReceive('getEmail')->andReturn('email');
        $user->shouldReceive('getDisplayName')->andReturn('display');
        $user->shouldReceive('getPronouns')->andReturn('pronouns');
        $user->shouldReceive('getPhone')->andReturn('phone');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('setExamined')->with(true);

        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true])
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
        $this->commandTester->execute([]);

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


    public function testExecuteWithNoAuthenticationDataChange(): void
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
        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn(42);
        $user->shouldReceive('getFirstAndLastName')->andReturn('first last');
        $user->shouldReceive('getFirstName')->andReturn('first');
        $user->shouldReceive('getMiddleName')->andReturn(null);
        $user->shouldReceive('getLastName')->andReturn('last');
        $user->shouldReceive('getEmail')->andReturn('email');
        $user->shouldReceive('getDisplayName')->andReturn('display');
        $user->shouldReceive('getPronouns')->andReturn('pronouns');
        $user->shouldReceive('getPhone')->andReturn('phone');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('getAuthentication')->andReturn(null);
        $user->shouldReceive('setExamined')->with(true);

        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('setUser')->with($user);
        $authentication->shouldReceive('setUsername')->with('abc123');
        $authentication->shouldReceive('getUsername')->andReturn('');

        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true])
            ->andReturn([$user])
            ->once();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userRepository->shouldReceive('update')->with($user, false)->once();

        $this->authenticationRepository->shouldReceive('findOneBy')
            ->with(['username' => 'abc123'])->once()->andReturn(null);
        $this->authenticationRepository->shouldReceive('create')->andReturn($authentication)->once();
        $this->authenticationRepository->shouldReceive('update')->with($authentication, false)->once();
        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute([]);

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

    public function testExecuteWithMultipleUserMatches(): void
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
        $user1 = m::mock(UserInterface::class);
        $user1->shouldReceive('getId')->andReturn(42);
        $user1->shouldReceive('getFirstAndLastName')->andReturn('first last');
        $user1->shouldReceive('getFirstName')->andReturn('first');
        $user1->shouldReceive('getLastName')->andReturn('last');
        $user1->shouldReceive('getEmail')->andReturn('email');
        $user1->shouldReceive('getDisplayName')->andReturn('display');
        $user1->shouldReceive('getPronouns')->andReturn('pronouns');
        $user1->shouldReceive('getPhone')->andReturn('phone');
        $user1->shouldReceive('getCampusId')->andReturn('abc');
        $user1->shouldReceive('getAuthentication')->andReturn(null);
        $user1->shouldReceive('setExamined')->with(true);

        $user2 = m::mock(UserInterface::class);
        $user2->shouldReceive('getId')->andReturn(11);
        $user2->shouldReceive('getFirstAndLastName')->andReturn('other guy');
        $user2->shouldReceive('getFirstName')->andReturn('other');
        $user2->shouldReceive('getLastName')->andReturn('guy');
        $user2->shouldReceive('getEmail')->andReturn('other-guy');
        $user2->shouldReceive('getPhone')->andReturn('');
        $user2->shouldReceive('getCampusId')->andReturn('abc');
        $user2->shouldReceive('getAuthentication')->andReturn(null);
        $user2->shouldReceive('setExamined')->with(true);

        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true])
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
        $this->commandTester->execute([]);

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

    public function testExecuteWithEmptyFirstName(): void
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
        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn(42);
        $user->shouldReceive('getFirstAndLastName')->andReturn('missing person');
        $user->shouldReceive('getEmail')->andReturn('email');
        $user->shouldReceive('getDisplayName')->andReturn('display');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('setExamined')->with(true);

        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true])
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
        $this->commandTester->execute([]);

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

    public function testExecuteWithEmptyLastName(): void
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
        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn(42);
        $user->shouldReceive('getFirstAndLastName')->andReturn('missing person');
        $user->shouldReceive('getEmail')->andReturn('email');
        $user->shouldReceive('getDisplayName')->andReturn('display');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('setExamined')->with(true);

        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true])
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
        $this->commandTester->execute([]);

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

    public function testExecuteWithEmptyEmail(): void
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
        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn(42);
        $user->shouldReceive('getFirstAndLastName')->andReturn('missing person');
        $user->shouldReceive('getEmail')->andReturn('email');
        $user->shouldReceive('getDisplayName')->andReturn('display');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('setExamined')->with(true);

        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true])
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
        $this->commandTester->execute([]);

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

    public function testExecuteWithEmptyUsernamelName(): void
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
        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn(42);
        $user->shouldReceive('getFirstAndLastName')->andReturn('missing person');
        $user->shouldReceive('getEmail')->andReturn('email');
        $user->shouldReceive('getDisplayName')->andReturn('display');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('setExamined')->with(true);

        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true])
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
        $this->commandTester->execute([]);

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

    public function testExecuteWithUserNotInTheDirectory(): void
    {
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $this->directory
            ->shouldReceive('findByCampusIds')
            ->with(['abc'])
            ->andReturn([]);
        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn(42);
        $user->shouldReceive('getFirstAndLastName')->andReturn('missing person');
        $user->shouldReceive('getEmail')->andReturn('email');
        $user->shouldReceive('getDisplayName')->andReturn('display');
        $user->shouldReceive('getCampusId')->andReturn('abc');

        $this->userRepository
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([$user])
            ->once();

        $update = m::mock(PendingUserUpdateInterface::class);
        $update->shouldReceive('setType')->with('missingFromDirectory')->once();
        $update->shouldReceive('setUser')->with($user)->once();

        $this->pendingUserUpdateRepository->shouldReceive('create')->andReturn($update)->once();
        $this->pendingUserUpdateRepository->shouldReceive('update')->with($update, false)->once();

        $this->em->shouldReceive('flush')->once();
        $this->commandTester->execute([]);

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

    public function testExecuteEmailChangeDoesNotChangeOthers(): void
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
        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('getUsername')->andReturn('abc123');

        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn(42);
        $user->shouldReceive('getFirstAndLastName')->andReturn('first last');
        $user->shouldReceive('getFirstName')->andReturn('first');
        $user->shouldReceive('getMiddleName')->andReturn(null);
        $user->shouldReceive('getLastName')->andReturn('last');
        $user->shouldReceive('getEmail')->andReturn('email');
        $user->shouldReceive('getDisplayName')->andReturn('display');
        $user->shouldReceive('getPronouns')->andReturn('pronouns');
        $user->shouldReceive('getPhone')->andReturn('phone');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('setExamined')->with(true);

        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true])
            ->andReturn([$user])
            ->once();
        $this->userRepository
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userRepository->shouldReceive('update')->with($user, false)->once();
        $update = m::mock(PendingUserUpdateInterface::class);
        $update->shouldReceive('setType')->with('emailMismatch')->once();
        $update->shouldReceive('setProperty')->with('email')->once();
        $update->shouldReceive('setValue')->with('new-email')->once();
        $update->shouldReceive('setUser')->with($user)->once();

        $this->pendingUserUpdateRepository->shouldReceive('create')->andReturn($update)->once();
        $this->pendingUserUpdateRepository->shouldReceive('update')->with($update, false)->once();

        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Completed sync process 1 users found in the directory; 0 users updated./',
            $output
        );
    }

    public function testExecuteWithIgnoredAuthenticationWithSameUserName(): void
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
        $user1 = m::mock(UserInterface::class);
        $user1->shouldReceive('getId')->andReturn(42);
        $user1->shouldReceive('getFirstAndLastName')->andReturn('first last');
        $user1->shouldReceive('getFirstName')->andReturn('first');
        $user1->shouldReceive('getMiddleName')->andReturn(null);
        $user1->shouldReceive('getLastName')->andReturn('last');
        $user1->shouldReceive('getEmail')->andReturn('email');
        $user1->shouldReceive('getDisplayName')->andReturn('display');
        $user1->shouldReceive('getPronouns')->andReturn('pronouns');
        $user1->shouldReceive('getPhone')->andReturn('phone');
        $user1->shouldReceive('getCampusId')->andReturn('abc');
        $user1->shouldReceive('getAuthentication')->andReturn(null);
        $user1->shouldReceive('setExamined')->with(true);

        $user2 = m::mock(UserInterface::class);
        $user2->shouldReceive('getId')->andReturn(11);
        $user2->shouldReceive('getFirstAndLastName')->andReturn('other guy');
        $user2->shouldReceive('getFirstName')->andReturn('other');
        $user2->shouldReceive('getMiddleName')->andReturn(null);
        $user2->shouldReceive('getLastName')->andReturn('guy');
        $user2->shouldReceive('getEmail')->andReturn('other-guy');
        $user2->shouldReceive('getPhone')->andReturn('');
        $user2->shouldReceive('getCampusId')->andReturn('abc');
        $user2->shouldReceive('getAuthentication')->andReturn(null);
        $user2->shouldReceive('setExamined')->with(true);

        $duplicateAuthentication = m::mock(AuthenticationInterface::class);
        $duplicateAuthentication->shouldReceive('getUser')->andReturn($user2);

        $this->userRepository
            ->shouldReceive('findBy')
            ->with(['campusId' => 'abc', 'enabled' => true])
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
        $this->commandTester->execute([]);

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
