<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\Attributes\Group;
use App\Command\AddNewStudentsToSchoolCommand;
use App\Entity\AuthenticationInterface;
use App\Entity\SchoolInterface;
use App\Entity\UserInterface;
use App\Entity\UserRoleInterface;
use App\Repository\AuthenticationRepository;
use App\Repository\SchoolRepository;
use App\Repository\UserRepository;
use App\Repository\UserRoleRepository;
use App\Service\Directory;
use Exception;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class AddNewStudentsToSchoolCommandTest
 */
#[Group('cli')]
class AddNewStudentsToSchoolCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected m\MockInterface $userRepository;
    protected m\MockInterface $userRolerepository;
    protected m\MockInterface $schoolRepository;
    protected m\MockInterface $authenticationRepository;
    protected CommandTester $commandTester;
    protected m\MockInterface $directory;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = m::mock(UserRepository::class);
        $this->userRolerepository = m::mock(UserRoleRepository::class);
        $this->schoolRepository = m::mock(SchoolRepository::class);
        $this->authenticationRepository = m::mock(AuthenticationRepository::class);
        $this->directory = m::mock(Directory::class);

        $command = new AddNewStudentsToSchoolCommand(
            $this->userRepository,
            $this->schoolRepository,
            $this->authenticationRepository,
            $this->userRolerepository,
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
        unset($this->userRolerepository);
        unset($this->schoolRepository);
        unset($this->authenticationRepository);
        unset($this->directory);
        unset($this->commandTester);
    }

    public function testExecute(): void
    {
        $fakeDirectoryUser1 = [
            'firstName' => 'first',
            'lastName' => 'last',
            'displayName' => 'first last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'username',
            'preferredFirstName' => 'preferredFirst',
            'preferredLastName' => 'preferredLast',
        ];
        $fakeDirectoryUser2 = [
            'firstName' => 'first2',
            'lastName' => 'last2',
            'displayName' => 'first2 last2',
            'email' => 'email2',
            'telephoneNumber' => 'phone2',
            'campusId' => 'abc2',
            'username' => 'username2',
            'preferredFirstName' => null,
            'preferredLastName' => null,
        ];
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getTitle')->andReturn('school 1');

        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn(42);
        $user->shouldReceive('getFirstName')->andReturn('first');
        $user->shouldReceive('getFirstAndLastName')->andReturn('first last');
        $user->shouldReceive('getEmail')->andReturn('email');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('setFirstName')->with('preferredFirst');
        $user->shouldReceive('setLastName')->with('preferredLast');
        $user->shouldReceive('setDisplayName')->with('first last');
        $user->shouldReceive('setEmail')->with('email');
        $user->shouldReceive('setPhone')->with('phone');
        $user->shouldReceive('setCampusId')->with('abc');
        $user->shouldReceive('setAddedViaIlios')->with(true);
        $user->shouldReceive('setEnabled')->with(true);
        $user->shouldReceive('setUserSyncIgnore')->with(false);
        $user->shouldReceive('setSchool')->with($school);
        $user->shouldReceive('addRole')->with($school);

        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('setUser')->with($user);
        $authentication->shouldReceive('setUsername')->with('username');

        $this->directory->shouldReceive('findByLdapFilter')
            ->with('FILTER')
            ->andReturn([$fakeDirectoryUser1, $fakeDirectoryUser2]);
        $this->userRepository->shouldReceive('getAllCampusIds')
            ->andReturn(['abc2']);

        $this->userRepository->shouldReceive('create')->andReturn($user);
        $this->userRepository
            ->shouldReceive('update')
            ->with($user)->once();
        $this->schoolRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($school);
        $role = m::mock(UserRoleInterface::class);
        $role->shouldReceive('addUser')->with($user);

        $user->shouldReceive('addRole')->with($role);
        $this->userRolerepository
            ->shouldReceive('findOneBy')
            ->with(['title' => 'Student'])
            ->andReturn($role);
        $this->userRolerepository
            ->shouldReceive('update')
            ->with($role);

        $this->authenticationRepository->shouldReceive('create')->andReturn($authentication);
        $this->authenticationRepository->shouldReceive('update')->with($authentication, false);

        $this->commandTester->setInputs(['Yes']);
        $this->commandTester->execute([
            'schoolId' => 1,
            'filter' => 'FILTER',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Found 2 students in the directory./',
            $output
        );

        $this->assertMatchesRegularExpression(
            '/There are 1 new students to be added to school 1./',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Do you wish to add these students to school 1?/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/abc\s+\| preferredFirst\s+\| preferredLast\s+\| email /',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Success! New student #42 first last created./',
            $output
        );
    }

    public function testBadSchoolId(): void
    {
        $this->schoolRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn(null);
        $this->expectException(Exception::class);
        $this->commandTester->execute([
            'filter' => 'FILTER',
            'schoolId' => '1',
        ]);
    }

    public function testFilterRequired(): void
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
            'filter' => '1',
        ]);
    }
}
