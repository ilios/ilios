<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\SyncStudentStatusCommand;
use App\Entity\UserInterface;
use App\Entity\UserRoleInterface;
use App\Repository\UserRepository;
use App\Repository\UserRoleRepository;
use App\Service\Directory;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

class SyncStudentStatusCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private const COMMAND_NAME = 'ilios:sync-students';

    protected UserRepository|m\MockInterface $userRepository;
    protected UserRoleRepository|m\MockInterface $userRoleRepository;
    protected CommandTester $commandTester;
    protected QuestionHelper $questionHelper;
    protected Directory|m\MockInterface $directory;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = m::mock(UserRepository::class);
        $this->userRoleRepository = m::mock(UserRoleRepository::class);
        $this->directory = m::mock(Directory::class);

        $command = new SyncStudentStatusCommand($this->userRepository, $this->userRoleRepository, $this->directory);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
        $this->questionHelper = $command->getHelper('question');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->userRepository);
        unset($this->userRoleRepository);
        unset($this->directory);
        unset($this->commandTester);
    }

    public function testExecute()
    {
        $fakeDirectoryUser1 = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
        ];
        $fakeDirectoryUser2 = [
            'firstName' => 'first2',
            'lastName' => 'last2',
            'email' => 'email2',
            'telephoneNumber' => 'phone2',
            'campusId' => 'abc2',
        ];
        $role = m::mock(UserRoleInterface::class);
        $user = m::mock(UserInterface::class)
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstName')->andReturn('first')
            ->shouldReceive('getLastName')->andReturn('last')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('isUserSyncIgnore')->andReturn(false)
            ->shouldReceive('isEnabled')->andReturn(true)
            ->shouldReceive('addRole')->with($role)->once()
            ->mock();
        $this->directory->shouldReceive('findByLdapFilter')
            ->with('FILTER')
            ->andReturn([$fakeDirectoryUser1, $fakeDirectoryUser2]);
        $this->userRepository->shouldReceive('findUsersWhoAreNotStudents')
            ->with(['abc', 'abc2'])
            ->andReturn([$user]);
        $this->userRepository->shouldReceive('update')
            ->with($user, false)->once();
        $this->userRepository->shouldReceive('flush')->once();
        $this->userRoleRepository
            ->shouldReceive('findOneBy')
            ->with(['title' => 'Student'])
            ->andReturn($role);

        $this->commandTester->setInputs(['Yes']);
        $this->commandTester->execute([
            'command'   => self::COMMAND_NAME,
            'filter'    => 'FILTER'
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Found 2 students in the directory./',
            $output
        );

        $this->assertMatchesRegularExpression(
            '/There are 1 students in Ilios who will be marked as a Student./',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Do you wish to mark these users as Students?/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/abc\s+\| first\s+\| last\s+\| email /',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Students updated successfully!/',
            $output
        );
    }

    public function testFilterRequired()
    {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute(['command' => self::COMMAND_NAME]);
    }
}
