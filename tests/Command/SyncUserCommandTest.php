<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\SyncUserCommand;
use App\Entity\AuthenticationInterface;
use App\Entity\PendingUserUpdateInterface;
use App\Entity\UserInterface;
use App\Repository\AuthenticationRepository;
use App\Repository\PendingUserUpdateRepository;
use App\Repository\UserRepository;
use App\Service\Directory;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class SyncUserCommandTest
 */
#[\PHPUnit\Framework\Attributes\Group('cli')]
class SyncUserCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected m\MockInterface $userRepository;
    protected m\MockInterface $authenticationRepository;
    protected m\MockInterface $pendingUserUpdateRepository;
    protected CommandTester $commandTester;
    protected m\MockInterface $directory;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = m::mock(UserRepository::class);
        $this->authenticationRepository = m::mock(AuthenticationRepository::class);
        $this->pendingUserUpdateRepository = m::mock(PendingUserUpdateRepository::class);
        $this->directory = m::mock(Directory::class);

        $command = new SyncUserCommand(
            $this->userRepository,
            $this->authenticationRepository,
            $this->pendingUserUpdateRepository,
            $this->directory
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
        unset($this->pendingUserUpdateRepository);
        unset($this->directory);
        unset($this->commandTester);
    }

    public function testExecute(): void
    {
        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('setUsername')->with('username');

        $pendingUpdate = m::mock(PendingUserUpdateInterface::class);
        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getFirstName')->andReturn('old-first');
        $user->shouldReceive('getPendingUserUpdates')->andReturn(new ArrayCollection([$pendingUpdate]));
        $user->shouldReceive('getMiddleName')->andReturn('old-middle');
        $user->shouldReceive('getLastName')->andReturn('old-last');
        $user->shouldReceive('getEmail')->andReturn('old-email');
        $user->shouldReceive('getDisplayName')->andReturn('old-display');
        $user->shouldReceive('getPronouns')->andReturn('old-pronouns');
        $user->shouldReceive('getPhone')->andReturn('old-phone');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('setFirstName')->with('first');
        $user->shouldReceive('setMiddleName')->with('middle');
        $user->shouldReceive('setLastName')->with('last');
        $user->shouldReceive('setEmail')->with('email');
        $user->shouldReceive('setDisplayName')->with('display');
        $user->shouldReceive('setPronouns')->with('pronouns');
        $user->shouldReceive('setPhone')->with('phone');

        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($user);
        $this->userRepository->shouldReceive('update')->with($user);
        $this->authenticationRepository->shouldReceive('update')->with($authentication, false);
        $this->pendingUserUpdateRepository->shouldReceive('delete')->with($pendingUpdate)->once();
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
        $this->directory->shouldReceive('findByCampusId')->with('abc')->andReturn($fakeDirectoryUser);
        $this->commandTester->setInputs(['Yes']);

        $this->commandTester->execute([
            'userId' => '1',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Ilios User\s+\| ' .
            'abc\s+\| ' .
            'old-first\s+\| ' .
            'old-middle\s+\| ' .
            'old-last\s+\| ' .
            'old-display\s+\| ' .
            'old-pronouns\s+\| ' .
            'old-email\s+\| ' .
            'old-phone/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Directory User\s+\| ' .
            'abc\s+\| ' .
            'first\s+\| ' .
            'middle\s+\| ' .
            'last\s+\| ' .
            'display\s+\| ' .
            'pronouns\s+\| ' .
            'email\s+\| ' .
            'phone/',
            $output
        );
    }

    public function testExecuteWithPreferredNames(): void
    {
        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('setUsername')->with('username');

        $pendingUpdate = m::mock(PendingUserUpdateInterface::class);
        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getFirstName')->andReturn('old-first');
        $user->shouldReceive('getPendingUserUpdates')->andReturn(new ArrayCollection([$pendingUpdate]));
        $user->shouldReceive('getMiddleName')->andReturn('old-middle');
        $user->shouldReceive('getLastName')->andReturn('old-last');
        $user->shouldReceive('getEmail')->andReturn('old-email');
        $user->shouldReceive('getDisplayName')->andReturn('old-display');
        $user->shouldReceive('getPronouns')->andReturn('old-pronouns');
        $user->shouldReceive('getPhone')->andReturn('old-phone');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('setFirstName')->with('preferred-first');
        $user->shouldReceive('setMiddleName')->with('preferred-middle');
        $user->shouldReceive('setLastName')->with('preferred-last');
        $user->shouldReceive('setEmail')->with('email');
        $user->shouldReceive('setDisplayName')->with('display');
        $user->shouldReceive('setPronouns')->with('pronouns');
        $user->shouldReceive('setPhone')->with('phone');

        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($user);
        $this->userRepository->shouldReceive('update')->with($user);
        $this->authenticationRepository->shouldReceive('update')->with($authentication, false);
        $this->pendingUserUpdateRepository->shouldReceive('delete')->with($pendingUpdate)->once();
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'middleName' => 'middle',
            'lastName' => 'last',
            'preferredFirstName' => 'preferred-first',
            'preferredMiddleName' => 'preferred-middle',
            'preferredLastName' => 'preferred-last',
            'email' => 'email',
            'displayName' => 'display',
            'pronouns' => 'pronouns',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'username',
        ];
        $this->directory->shouldReceive('findByCampusId')->with('abc')->andReturn($fakeDirectoryUser);
        $this->commandTester->setInputs(['Yes']);

        $this->commandTester->execute([
            'userId' => '1',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Ilios User\s+\| ' .
            'abc\s+\| ' .
            'old-first\s+\| ' .
            'old-middle\s+\| ' .
            'old-last\s+\| ' .
            'old-display\s+\| ' .
            'old-pronouns\s+\| ' .
            'old-email\s+\| ' .
            'old-phone/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Directory User\s+\| ' .
            'abc\s+\| ' .
            'preferred-first\s+\| ' .
            'preferred-middle\s+\| ' .
            'preferred-last\s+\| ' .
            'display\s+\| ' .
            'pronouns\s+\| ' .
            'email\s+\| ' .
            'phone/',
            $output
        );
    }
    public function testEmptyPronouns(): void
    {
        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('setUsername')->with('username');
        $pendingUpdate = m::mock(PendingUserUpdateInterface::class);
        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getFirstName')->andReturn('old-first');
        $user->shouldReceive('getMiddleName')->andReturn(null);
        $user->shouldReceive('getPendingUserUpdates')->andReturn(new ArrayCollection([$pendingUpdate]));
        $user->shouldReceive('getLastName')->andReturn('old-last');
        $user->shouldReceive('getEmail')->andReturn('old-email');
        $user->shouldReceive('getDisplayName')->andReturn('old-display');
        $user->shouldReceive('getPronouns')->andReturn('old-pronouns');
        $user->shouldReceive('getPhone')->andReturn('old-phone');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('setFirstName')->with('first');
        $user->shouldReceive('setMiddleName')->with(null);
        $user->shouldReceive('setLastName')->with('last');
        $user->shouldReceive('setEmail')->with('email');
        $user->shouldReceive('setDisplayName')->with('display');
        $user->shouldReceive('setPronouns')->with(null);
        $user->shouldReceive('setPhone')->with('phone');

        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($user);
        $this->userRepository->shouldReceive('update')->with($user);
        $this->authenticationRepository->shouldReceive('update')->with($authentication, false);
        $this->pendingUserUpdateRepository->shouldReceive('delete')->with($pendingUpdate)->once();
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'displayName' => 'display',
            'pronouns' => null,
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'username',
        ];
        $this->directory->shouldReceive('findByCampusId')->with('abc')->andReturn($fakeDirectoryUser);
        $this->commandTester->setInputs(['Yes']);

        $this->commandTester->execute([
            'userId' => '1',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Ilios User\s+\| ' .
            'abc\s+\| ' .
            'old-first\s+\| ' .
            '\s+\| ' .
            'old-last\s+\| ' .
            'old-display\s+\| ' .
            'old-pronouns\s+\| ' .
            'old-email\s+\| ' .
            'old-phone/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Directory User\s+\| abc\s+\| first\s+\| \s+\| last\s+\| display\s+\| \s+\| email\s+\| phone/',
            $output
        );
    }
    public function testEmptyPreferredMiddle(): void
    {
        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('setUsername')->with('username');
        $pendingUpdate = m::mock(PendingUserUpdateInterface::class);
        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getFirstName')->andReturn('old-first');
        $user->shouldReceive('getPendingUserUpdates')->andReturn(new ArrayCollection([$pendingUpdate]));
        $user->shouldReceive('getMiddleName')->andReturn('old-middle');
        $user->shouldReceive('getLastName')->andReturn('old-last');
        $user->shouldReceive('getEmail')->andReturn('old-email');
        $user->shouldReceive('getDisplayName')->andReturn('old-display');
        $user->shouldReceive('getPronouns')->andReturn('old-pronouns');
        $user->shouldReceive('getPhone')->andReturn('old-phone');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('setFirstName')->with('first');
        $user->shouldReceive('setMiddleName')->with('');
        $user->shouldReceive('setLastName')->with('last');
        $user->shouldReceive('setEmail')->with('email');
        $user->shouldReceive('setDisplayName')->with('display');
        $user->shouldReceive('setPronouns')->with('pronouns');
        $user->shouldReceive('setPhone')->with('phone');

        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($user);
        $this->userRepository->shouldReceive('update')->with($user);
        $this->authenticationRepository->shouldReceive('update')->with($authentication, false);
        $this->pendingUserUpdateRepository->shouldReceive('delete')->with($pendingUpdate)->once();
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'middleName' => 'middle',
            'lastName' => 'last',
            'preferredMiddleName' => '',
            'email' => 'email',
            'displayName' => 'display',
            'pronouns' => 'pronouns',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'username',
        ];
        $this->directory->shouldReceive('findByCampusId')->with('abc')->andReturn($fakeDirectoryUser);
        $this->commandTester->setInputs(['Yes']);

        $this->commandTester->execute([
            'userId' => '1',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Ilios User\s+\| ' .
            'abc\s+\| ' .
            'old-first\s+\| ' .
            'old-middle\s+\| ' .
            'old-last\s+\| ' .
            'old-display\s+\| ' .
            'old-pronouns\s+\| ' .
            'old-email\s+\| ' .
            'old-phone/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Directory User\s+\| abc\s+\| first\s+\| \s+\| last\s+\| display\s+\| pronouns\s+\| email\s+\| phone/',
            $output
        );
    }
    public function testMiddleNotInDirectory(): void
    {
        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('setUsername')->with('username');
        $pendingUpdate = m::mock(PendingUserUpdateInterface::class);
        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getFirstName')->andReturn('old-first');
        $user->shouldReceive('getPendingUserUpdates')->andReturn(new ArrayCollection([$pendingUpdate]));
        $user->shouldReceive('getMiddleName')->andReturn('old-middle');
        $user->shouldReceive('getLastName')->andReturn('old-last');
        $user->shouldReceive('getEmail')->andReturn('old-email');
        $user->shouldReceive('getDisplayName')->andReturn('old-display');
        $user->shouldReceive('getPronouns')->andReturn('old-pronouns');
        $user->shouldReceive('getPhone')->andReturn('old-phone');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('setFirstName')->with('first');
        $user->shouldReceive('setMiddleName')->with(null);
        $user->shouldReceive('setLastName')->with('last');
        $user->shouldReceive('setEmail')->with('email');
        $user->shouldReceive('setDisplayName')->with('display');
        $user->shouldReceive('setPronouns')->with('pronouns');
        $user->shouldReceive('setPhone')->with('phone');

        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($user);
        $this->userRepository->shouldReceive('update')->with($user);
        $this->authenticationRepository->shouldReceive('update')->with($authentication, false);
        $this->pendingUserUpdateRepository->shouldReceive('delete')->with($pendingUpdate)->once();
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
        $this->directory->shouldReceive('findByCampusId')->with('abc')->andReturn($fakeDirectoryUser);
        $this->commandTester->setInputs(['Yes']);

        $this->commandTester->execute([
            'userId' => '1',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Ilios User\s+\| ' .
            'abc\s+\| ' .
            'old-first\s+\| ' .
            'old-middle\s+\| ' .
            'old-last\s+\| ' .
            'old-display\s+\| ' .
            'old-pronouns\s+\| ' .
            'old-email\s+\| ' .
            'old-phone/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Directory User\s+\| abc\s+\| first\s+\| \s+\| last\s+\| display\s+\| pronouns\s+\| email\s+\| phone/',
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
