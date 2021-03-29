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
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class SyncUserCommandTest
 * @group cli
 */
class SyncUserCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private const COMMAND_NAME = 'ilios:sync-user';

    protected $userRepository;
    protected $authenticationRepository;
    protected $pendingUserUpdateRepository;
    protected $commandTester;
    protected $questionHelper;
    protected $directory;

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
        unset($this->pendingUserUpdateRepository);
        unset($this->directory);
        unset($this->commandTester);
    }

    public function testExecute()
    {
        $authentication = m::mock(AuthenticationInterface::class)
            ->shouldReceive('setUsername')->with('username')
            ->mock();
        $pendingUpdate = m::mock(PendingUserUpdateInterface::class);
        $user = m::mock(UserInterface::class)
            ->shouldReceive('getFirstName')->andReturn('old-first')
            ->shouldReceive('getPendingUserUpdates')->andReturn([$pendingUpdate])
            ->shouldReceive('getLastName')->andReturn('old-last')
            ->shouldReceive('getEmail')->andReturn('old-email')
            ->shouldReceive('getDisplayName')->andReturn('old-display')
            ->shouldReceive('getPhone')->andReturn('old-phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('setFirstName')->with('first')
            ->shouldReceive('setLastName')->with('last')
            ->shouldReceive('setEmail')->with('email')
            ->shouldReceive('setDisplayName')->with('display')
            ->shouldReceive('setPhone')->with('phone')
            ->mock();
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($user);
        $this->userRepository->shouldReceive('update')->with($user);
        $this->authenticationRepository->shouldReceive('update')->with($authentication, false);
        $this->pendingUserUpdateRepository->shouldReceive('delete')->with($pendingUpdate)->once();
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'displayName' => 'display',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'username'
        ];
        $this->directory->shouldReceive('findByCampusId')->with('abc')->andReturn($fakeDirectoryUser);
        $this->commandTester->setInputs(['Yes']);

        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Ilios User\s+\| abc\s+\| old-first\s+\| old-last\s+\| old-display\s+\| old-email\s+\| old-phone/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Directory User\s+\| abc\s+\| first\s+\| last\s+\| display\s+\| email\s+\| phone/',
            $output
        );
    }

    public function testBadUserId()
    {
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn(null);
        $this->expectException(\Exception::class);
        $this->commandTester->execute([
            'command' => self::COMMAND_NAME,
            'userId' => '1'
        ]);
    }

    public function testUserRequired()
    {
        $this->expectException(\RuntimeException::class);
        $this->commandTester->execute(['command' => self::COMMAND_NAME]);
    }
}
