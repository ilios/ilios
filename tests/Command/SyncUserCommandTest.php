<?php
namespace App\Tests\Command;

use App\Command\SyncUserCommand;
use App\Entity\AuthenticationInterface;
use App\Entity\Manager\AuthenticationManager;
use App\Entity\Manager\PendingUserUpdateManager;
use App\Entity\Manager\UserManager;
use App\Entity\PendingUserUpdateInterface;
use App\Entity\UserInterface;
use App\Service\Directory;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class SyncUserCommandTest
 */
class SyncUserCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:sync-user';
    
    protected $userManager;
    protected $authenticationManager;
    protected $pendingUserUpdateManager;
    protected $commandTester;
    protected $questionHelper;
    protected $directory;
    
    public function setUp()
    {
        $this->userManager = m::mock(UserManager::class);
        $this->authenticationManager = m::mock(AuthenticationManager::class);
        $this->pendingUserUpdateManager = m::mock(PendingUserUpdateManager::class);
        $this->directory = m::mock(Directory::class);
        
        $command = new SyncUserCommand(
            $this->userManager,
            $this->authenticationManager,
            $this->pendingUserUpdateManager,
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
    public function tearDown() : void
    {
        unset($this->userManager);
        unset($this->authenticationManager);
        unset($this->pendingUserUpdateManager);
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
        $this->userManager->shouldReceive('findOneBy')->with(array('id' => 1))->andReturn($user);
        $this->userManager->shouldReceive('update')->with($user);
        $this->authenticationManager->shouldReceive('update')->with($authentication, false);
        $this->pendingUserUpdateManager->shouldReceive('delete')->with($pendingUpdate)->once();
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
        
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Ilios User\s+\| abc\s+\| old-first\s+\| old-last\s+\| old-display\s+\| old-email\s+\| old-phone/',
            $output
        );
        $this->assertRegExp(
            '/Directory User\s+\| abc\s+\| first\s+\| last\s+\| display\s+\| email\s+\| phone/',
            $output
        );
    }
    
    public function testBadUserId()
    {
        $this->userManager->shouldReceive('findOneBy')->with(array('id' => 1))->andReturn(null);
        $this->expectException(\Exception::class, 'No user with id #1');
        $this->commandTester->execute(array(
            'command' => self::COMMAND_NAME,
            'userId' => '1'
        ));
    }
    
    public function testUserRequired()
    {
        $this->expectException(\RuntimeException::class);
        $this->commandTester->execute(array('command' => self::COMMAND_NAME));
    }
}
