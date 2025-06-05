<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\Attributes\Group;
use App\Command\SyncFormerStudentsCommand;
use App\Entity\UserInterface;
use App\Entity\UserRoleInterface;
use App\Repository\UserRepository;
use App\Repository\UserRoleRepository;
use App\Service\Directory;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;

/**
 * Class SyncFormerStudentsCommandTest
 * @package App\Tests\Command
 */
#[Group('cli')]
final class SyncFormerStudentsCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected m\MockInterface $userRepository;
    protected m\MockInterface $userRoleRepository;
    protected CommandTester $commandTester;
    protected m\MockInterface $directory;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = m::mock(UserRepository::class);
        $this->userRoleRepository = m::mock(UserRoleRepository::class);
        $this->directory = m::mock(Directory::class);

        $command = new SyncFormerStudentsCommand($this->userRepository, $this->userRoleRepository, $this->directory);
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
        unset($this->userRoleRepository);
        unset($this->directory);
        unset($this->commandTester);
    }

    public function testExecute(): void
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
        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn(42);
        $user->shouldReceive('getFirstName')->andReturn('first');
        $user->shouldReceive('getLastName')->andReturn('last');
        $user->shouldReceive('getEmail')->andReturn('email');
        $user->shouldReceive('getCampusId')->andReturn('abc');
        $user->shouldReceive('isUserSyncIgnore')->andReturn(false);

        $this->directory->shouldReceive('findByLdapFilter')
            ->with('FILTER')
            ->andReturn([$fakeDirectoryUser1, $fakeDirectoryUser2]);
        $this->userRepository->shouldReceive('findUsersWhoAreNotFormerStudents')
            ->with(['abc', 'abc2'])
            ->andReturn(new ArrayCollection([$user]));
        $this->userRepository->shouldReceive('update')
            ->with($user, false);
        $role = m::mock(UserRoleInterface::class);
        $role->shouldReceive('addUser')->with($user);

        $user->shouldReceive('addRole')->with($role);
        $this->userRoleRepository
            ->shouldReceive('findOneBy')
            ->with(['title' => 'Former Student'])
            ->andReturn($role);
        $this->userRoleRepository
            ->shouldReceive('update')
            ->with($role);

        $this->commandTester->setInputs(['Yes']);
        $this->commandTester->execute([
            'filter' => 'FILTER',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Found 2 former students in the directory./',
            $output
        );

        $this->assertMatchesRegularExpression(
            '/There are 1 students in Ilios who will be marked as a Former Student./',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Do you wish to mark these users as Former Students?/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/abc\s+\| first\s+\| last\s+\| email /',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Former students updated successfully!/',
            $output
        );
    }

    public function testFilterRequired(): void
    {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([]);
    }
}
