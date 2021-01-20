<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\AddDirectoryUserCommand;
use App\Repository\AuthenticationRepository;
use App\Repository\SchoolRepository;
use App\Repository\UserRepository;
use App\Service\Directory;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class AddDirectoryUserCommandTest
 * @group cli
 */
class AddDirectoryUserCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private const COMMAND_NAME = 'ilios:add-directory-user';

    protected $userRepository;
    protected $authenticationRepository;
    protected $schoolRepository;
    protected $commandTester;
    protected $questionHelper;
    protected $directory;

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
        $commandInApp = $application->find(self::COMMAND_NAME);
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
        unset($this->directory);
        unset($this->commandTester);
        unset($this->questionHelper);
    }

    public function testExecute()
    {
        $school = m::mock('App\Entity\SchoolInterface');
        $authentication = m::mock('App\Entity\AuthenticationInterface')
            ->shouldReceive('setUsername')->with('abc123')
            ->mock();
        $user = m::mock('App\Entity\UserInterface')
            ->shouldReceive('setFirstName')->with('first')
            ->shouldReceive('setLastName')->with('last')
            ->shouldReceive('setEmail')->with('email')
            ->shouldReceive('setPhone')->with('phone')
            ->shouldReceive('setCampusId')->with('abc')
            ->shouldReceive('setAddedViaIlios')->with(true)
            ->shouldReceive('setEnabled')->with(true)
            ->shouldReceive('setUserSyncIgnore')->with(false)
            ->shouldReceive('setSchool')->with($school)
            ->shouldReceive('getId')->andReturn(1)
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('getFirstAndLastName')->andReturn('Test Person')
            ->mock();
        $authentication->shouldReceive('setUser')->with($user);

        $this->userRepository->shouldReceive('findOneBy')->with(['campusId' => 'abc'])->andReturn(false);
        $this->schoolRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($school);
        $this->userRepository->shouldReceive('create')->andReturn($user);
        $this->userRepository->shouldReceive('update')->with($user);
        $this->authenticationRepository->shouldReceive('create')->andReturn($authentication);
        $this->authenticationRepository->shouldReceive('update')->with($authentication);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'abc123'
        ];
        $this->directory->shouldReceive('findByCampusId')->with('abc')->andReturn($fakeDirectoryUser);
        $this->commandTester->setInputs(['Yes']);

        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'campusId'         => 'abc',
            'schoolId'         => '1',
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/abc\s+\| first\s+\| last\s+\| email\s+\| abc123\s+\| phone/',
            $output
        );
        $this->assertRegExp(
            '/Success! New user #1 Test Person created./',
            $output
        );
    }


    public function testBadCampusId()
    {
        $user = m::mock('App\Entity\UserInterface')
            ->shouldReceive('getId')->andReturn(1)
            ->mock();
        $this->userRepository->shouldReceive('findOneBy')->with(['campusId' => 1])->andReturn($user);
        $this->expectException(\Exception::class);
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'campusId'         => '1',
            'schoolId'         => '1'
        ]);
    }

    public function testBadSchoolId()
    {
        $this->userRepository->shouldReceive('findOneBy')->with(['campusId' => 1])->andReturn(null);
        $this->schoolRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn(null);
        $this->expectException(\Exception::class);
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'campusId'         => '1',
            'schoolId'         => '1'
        ]);
    }

    public function testUserRequired()
    {
        $this->expectException(\RuntimeException::class);
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'schoolId'         => '1'
        ]);
    }

    public function testSchoolRequired()
    {
        $this->expectException(\RuntimeException::class);
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'campusId'         => '1',
        ]);
    }
}
