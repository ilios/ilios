<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\AddDirectoryUserCommand;
use App\Entity\AuthenticationInterface;
use App\Entity\SchoolInterface;
use App\Entity\UserInterface;
use App\Repository\AuthenticationRepository;
use App\Repository\SchoolRepository;
use App\Repository\UserRepository;
use App\Service\Directory;
use Exception;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class AddDirectoryUserCommandTest
 */
#[\PHPUnit\Framework\Attributes\Group('cli')]
class AddDirectoryUserCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected m\MockInterface $userRepository;
    protected m\MockInterface $authenticationRepository;
    protected m\MockInterface $schoolRepository;
    protected CommandTester $commandTester;
    protected m\MockInterface $directory;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = m::mock(UserRepository::class);
        $this->authenticationRepository = m::mock(AuthenticationRepository::class);
        $this->schoolRepository = m::mock(SchoolRepository::class);
        $this->directory = m::mock(Directory::class);

        $command = new AddDirectoryUserCommand(
            $this->userRepository,
            $this->authenticationRepository,
            $this->schoolRepository,
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
        unset($this->schoolRepository);
        unset($this->directory);
        unset($this->commandTester);
    }

    public function testExecute(): void
    {
        $school = m::mock(SchoolInterface::class);
        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('setUsername')->with('abc123');

        $user = m::mock(UserInterface::class);
        $user->shouldReceive('setFirstName')->with('first');
        $user->shouldReceive('setLastName')->with('last');
        $user->shouldReceive('setDisplayName')->with('first last');
        $user->shouldReceive('setEmail')->with('email');
        $user->shouldReceive('setPhone')->with('phone');
        $user->shouldReceive('setCampusId')->with('abc');
        $user->shouldReceive('setAddedViaIlios')->with(true);
        $user->shouldReceive('setEnabled')->with(true);
        $user->shouldReceive('setUserSyncIgnore')->with(false);
        $user->shouldReceive('setSchool')->with($school);
        $user->shouldReceive('getId')->andReturn(1);
        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('getFirstAndLastName')->andReturn('Test Person');

        $authentication->shouldReceive('setUser')->with($user);

        $this->userRepository->shouldReceive('findOneBy')->with(['campusId' => 'abc'])->andReturn(null);
        $this->schoolRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($school);
        $this->userRepository->shouldReceive('create')->andReturn($user);
        $this->userRepository->shouldReceive('update')->with($user);
        $this->authenticationRepository->shouldReceive('create')->andReturn($authentication);
        $this->authenticationRepository->shouldReceive('update')->with($authentication);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'displayName' => 'first last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'abc123',
            'preferredFirstName' => null,
            'preferredLastName' => null,
        ];
        $this->directory->shouldReceive('findByCampusId')->with('abc')->andReturn($fakeDirectoryUser);
        $this->commandTester->setInputs(['Yes']);

        $this->commandTester->execute([
            'campusId' => 'abc',
            'schoolId' => '1',
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/abc\s+\| first\s+\| last\s+\| first last\s+\| email\s+\| abc123\s+\| phone/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Success! New user #1 Test Person created./',
            $output
        );
    }
    public function testExecuteForUserWithPreferredName(): void
    {
        $school = m::mock(SchoolInterface::class);
        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('setUsername')->with('abc123');

        $user = m::mock(UserInterface::class);
        $user->shouldReceive('setFirstName')->with('preferred first');
        $user->shouldReceive('setLastName')->with('preferred last');
        $user->shouldReceive('setDisplayName')->with('first last');
        $user->shouldReceive('setEmail')->with('email');
        $user->shouldReceive('setPhone')->with('phone');
        $user->shouldReceive('setCampusId')->with('abc');
        $user->shouldReceive('setAddedViaIlios')->with(true);
        $user->shouldReceive('setEnabled')->with(true);
        $user->shouldReceive('setUserSyncIgnore')->with(false);
        $user->shouldReceive('setSchool')->with($school);
        $user->shouldReceive('getId')->andReturn(1);
        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('getFirstAndLastName')->andReturn('Test Person');

        $authentication->shouldReceive('setUser')->with($user);

        $this->userRepository->shouldReceive('findOneBy')->with(['campusId' => 'abc'])->andReturn(null);
        $this->schoolRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($school);
        $this->userRepository->shouldReceive('create')->andReturn($user);
        $this->userRepository->shouldReceive('update')->with($user);
        $this->authenticationRepository->shouldReceive('create')->andReturn($authentication);
        $this->authenticationRepository->shouldReceive('update')->with($authentication);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'displayName' => 'first last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'abc123',
            'preferredFirstName' => 'preferred first',
            'preferredLastName' => 'preferred last',
        ];
        $this->directory->shouldReceive('findByCampusId')->with('abc')->andReturn($fakeDirectoryUser);
        $this->commandTester->setInputs(['Yes']);

        $this->commandTester->execute([
            'campusId' => 'abc',
            'schoolId' => '1',
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/abc\s+\| preferred first\s+\| preferred last\s+\| first last\s+\| email\s+\| abc123\s+\| phone/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Success! New user #1 Test Person created./',
            $output
        );
    }

    public function testBadCampusId(): void
    {
        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn(1);

        $this->userRepository->shouldReceive('findOneBy')->with(['campusId' => 1])->andReturn($user);
        $this->expectException(Exception::class);
        $this->commandTester->execute([
            'campusId' => '1',
            'schoolId' => '1',
        ]);
    }

    public function testBadSchoolId(): void
    {
        $this->userRepository->shouldReceive('findOneBy')->with(['campusId' => 1])->andReturn(null);
        $this->schoolRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn(null);
        $this->expectException(Exception::class);
        $this->commandTester->execute([
            'campusId' => '1',
            'schoolId' => '1',
        ]);
    }

    public function testUserRequired(): void
    {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([
            'schoolId' => '1',
        ]);
    }

    public function testSchoolRequired(): void
    {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([
            'campusId' => '1',
        ]);
    }
}
