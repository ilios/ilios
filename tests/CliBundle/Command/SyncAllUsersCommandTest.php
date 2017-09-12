<?php
namespace Tests\CliBundle\Command;

use Ilios\CliBundle\Command\SyncAllUsersCommand;
use Ilios\CoreBundle\Entity\AuthenticationInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

use Mockery as m;
use PHPUnit\Framework\TestCase;

/**
 * Class SyncAllUsersCommandTest
 */
class SyncAllUsersCommandTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:directory:sync-users';
    
    protected $userManager;
    protected $authenticationManager;
    protected $pendingUserUpdateManager;
    protected $commandTester;
    protected $questionHelper;
    protected $directory;
    protected $em;
    
    public function setUp()
    {
        $this->userManager = m::mock('Ilios\CoreBundle\Entity\Manager\UserManager');
        $this->authenticationManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManager');
        $this->pendingUserUpdateManager = m::mock('Ilios\CoreBundle\Entity\Manager\PendingUserUpdateManager');
        $this->directory = m::mock('Ilios\CoreBundle\Service\Directory');
        $this->em = m::mock('Doctrine\Orm\EntityManager');
        
        $command = new SyncAllUsersCommand(
            $this->userManager,
            $this->authenticationManager,
            $this->pendingUserUpdateManager,
            $this->directory,
            $this->em
        );
        $application = new Application();
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
        $this->questionHelper = $command->getHelper('question');
        
        $this->pendingUserUpdateManager->shouldReceive('removeAllPendingUserUpdates')->once();
        $this->userManager->shouldReceive('resetExaminedFlagForAllUsers')->once();
    }

    /**
     * Remove all mock objects
     */
    public function tearDown()
    {
        unset($this->userManager);
        unset($this->authenticationManager);
        unset($this->pendingUserUpdateManager);
        unset($this->directory);
        unset($this->em);
        unset($this->commandTester);
    }
    
    public function testExecuteUserWithNoChanges()
    {
        $this->userManager->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
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
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(array('campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false))
            ->andReturn([$user])
            ->once();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())
            ->andReturn([])
            ->once();
        $this->userManager->shouldReceive('update')->with($user, false)->once();
        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Comparing User #42 first last \(email\) to directory user by campus ID abc./',
            $output
        );
        $this->assertRegExp(
            '/Completed sync process 1 users found in the directory; 0 users updated./',
            $output
        );
    }
    
    public function testExecuteWithFirstNameChange()
    {
        $this->userManager->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'new-first',
            'lastName' => 'last',
            'email' => 'email',
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
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('setExamined')->with(true)
            ->shouldReceive('setFirstName')->with('new-first')
            ->mock();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(array('campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false))
            ->andReturn([$user])
            ->once();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userManager->shouldReceive('update')->with($user, false)->once();
                
        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Updating first name from "first" to "new-first"./',
            $output
        );
        $this->assertRegExp(
            '/Completed sync process 1 users found in the directory; 1 users updated./',
            $output
        );
    }
    
    public function testExecuteWithLastNameChange()
    {
        $this->userManager->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'new-last',
            'email' => 'email',
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
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('setExamined')->with(true)
            ->shouldReceive('setLastName')->with('new-last')
            ->mock();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(array('campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false))
            ->andReturn([$user])
            ->once();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userManager->shouldReceive('update')->with($user, false)->once();
                
        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Updating last name from "last" to "new-last"./',
            $output
        );
        $this->assertRegExp(
            '/Completed sync process 1 users found in the directory; 1 users updated./',
            $output
        );
    }
    
    public function testExecuteWithPhoneNumberChange()
    {
        $this->userManager->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
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
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('setExamined')->with(true)
            ->shouldReceive('setPhone')->with('new-phone')
            ->mock();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(array('campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false))
            ->andReturn([$user])
            ->once();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userManager->shouldReceive('update')->with($user, false)->once();
                
        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Updating phone number from "phone" to "new-phone"./',
            $output
        );
        $this->assertRegExp(
            '/Completed sync process 1 users found in the directory; 1 users updated./',
            $output
        );
    }
    
    public function testExecuteWithEmailChange()
    {
        $this->userManager->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'new-email',
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
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(array('campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false))
            ->andReturn([$user])
            ->once();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userManager->shouldReceive('update')->with($user, false)->once();
        $update = m::mock('Ilios\CoreBundle\Entity\PendingUserUpdate')
            ->shouldReceive('setType')->with('emailMismatch')->once()
            ->shouldReceive('setProperty')->with('email')->once()
            ->shouldReceive('setValue')->with('new-email')->once()
            ->shouldReceive('setUser')->with($user)->once()
            ->mock();
        $this->pendingUserUpdateManager->shouldReceive('create')->andReturn($update)->once();
        $this->pendingUserUpdateManager->shouldReceive('update')->with($update, false)->once();
                
        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Email address "email" differs from "new-email" logging for further action./',
            $output
        );
        $this->assertRegExp(
            '/Completed sync process 1 users found in the directory; 0 users updated./',
            $output
        );
    }
    
    public function testExecuteWithEmailCaseChange()
    {
        $this->userManager->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'EMAIL',
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
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('setExamined')->with(true)
            ->shouldReceive('setEmail')->with('EMAIL')
            ->mock();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(array('campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false))
            ->andReturn([$user])
            ->once();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userManager->shouldReceive('update')->with($user, false)->once();

        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Updating email from "email" to "EMAIL" since the only difference was the case./',
            $output
        );
        $this->assertRegExp(
            '/Completed sync process 1 users found in the directory; 1 users updated./',
            $output
        );
    }
    
    public function testExecuteWithUsernameChange()
    {
        $this->userManager->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
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
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(array('campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false))
            ->andReturn([$user])
            ->once();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userManager->shouldReceive('update')->with($user, false)->once();
                
        $this->authenticationManager->shouldReceive('update')->with($authentication, false)->once();
        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Updating username from "abc123" to "new-abc123"./',
            $output
        );
        $this->assertRegExp(
            '/Completed sync process 1 users found in the directory; 1 users updated./',
            $output
        );
    }
    
    
    public function testExecuteWithNoAuthenticationDataChange()
    {
        $this->userManager->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
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
        $this->userManager
            ->shouldReceive('findBy')
            ->with(array('campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false))
            ->andReturn([$user])
            ->once();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userManager->shouldReceive('update')->with($user, false)->once();

        $this->authenticationManager->shouldReceive('findOneBy')
            ->with(['username' => 'abc123'])->once()->andReturn(false);
        $this->authenticationManager->shouldReceive('create')->andReturn($authentication)->once();
        $this->authenticationManager->shouldReceive('update')->with($authentication, false)->once();
        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Updating username from "" to "abc123"./',
            $output
        );
        $this->assertRegExp(
            '/User had no authentication data, creating it now./',
            $output
        );
        $this->assertRegExp(
            '/Completed sync process 1 users found in the directory; 1 users updated./',
            $output
        );
    }
    
    public function testExecuteWithMultipleUserMatches()
    {
        $this->userManager->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
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
        $this->userManager
            ->shouldReceive('findBy')
            ->with(array('campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false))
            ->andReturn([$user1, $user2])
            ->once();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userManager->shouldReceive('update')->with($user1, false)->once();
        $this->userManager->shouldReceive('update')->with($user2, false)->once();

        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Multiple accounts exist for the same campus ID \(abc\)\.  None of them will be updated./',
            $output
        );
        $this->assertRegExp(
            '/Completed sync process 1 users found in the directory; 0 users updated./',
            $output
        );
    }
    
    public function testExecuteWithEmptyFirstName()
    {
        $this->userManager->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => '',
            'lastName' => 'last',
            'email' => 'email',
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
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(array('campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false))
            ->andReturn([$user])
            ->once();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userManager->shouldReceive('update')->with($user, false)->once();

        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/firstName is required and it is missing from record with campus ID \(abc\)\.  User will not be updated./',
            $output
        );
        $this->assertRegExp(
            '/Completed sync process 1 users found in the directory; 0 users updated./',
            $output
        );
    }
    
    public function testExecuteWithEmptyLastName()
    {
        $this->userManager->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => '',
            'email' => 'email',
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
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(array('campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false))
            ->andReturn([$user])
            ->once();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userManager->shouldReceive('update')->with($user, false)->once();

        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/lastName is required and it is missing from record with campus ID \(abc\)\.  User will not be updated./',
            $output
        );
        $this->assertRegExp(
            '/Completed sync process 1 users found in the directory; 0 users updated./',
            $output
        );
    }
    
    public function testExecuteWithEmptyEmailName()
    {
        $this->userManager->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => '',
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
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(array('campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false))
            ->andReturn([$user])
            ->once();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userManager->shouldReceive('update')->with($user, false)->once();

        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/email is required and it is missing from record with campus ID \(abc\)\.  User will not be updated./',
            $output
        );
        $this->assertRegExp(
            '/Completed sync process 1 users found in the directory; 0 users updated./',
            $output
        );
    }
    
    public function testExecuteWithEmptyUsernamelName()
    {
        $this->userManager->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
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
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(array('campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false))
            ->andReturn([$user])
            ->once();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userManager->shouldReceive('update')->with($user, false)->once();

        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/username is required and it is missing from record with campus ID \(abc\)\.  User will not be updated./',
            $output
        );
        $this->assertRegExp(
            '/Completed sync process 1 users found in the directory; 0 users updated./',
            $output
        );
    }
    
    public function testExecuteWithUserNotInTheDirectory()
    {
        $this->userManager->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
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
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->mock();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([$user])
            ->once();
        
        $update = m::mock('Ilios\CoreBundle\Entity\PendingUserUpdate')
            ->shouldReceive('setType')->with('missingFromDirectory')->once()
            ->shouldReceive('setUser')->with($user)->once()
            ->mock();
        $this->pendingUserUpdateManager->shouldReceive('create')->andReturn($update)->once();
        $this->pendingUserUpdateManager->shouldReceive('update')->with($update, false)->once();
        
        $this->em->shouldReceive('flush')->once();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/User #42 missing person email abc not found in the directory.  Logged for further study/',
            $output
        );
        $this->assertRegExp(
            '/Completed sync process 0 users found in the directory; 0 users updated./',
            $output
        );
    }
    
    public function testExecuteEmailChangeDoesNotChangeOthers()
    {
        $this->userManager->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'new-first',
            'lastName' => 'new-last',
            'email' => 'new-email',
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
            ->shouldReceive('getPhone')->andReturn('phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('setExamined')->with(true)
            ->mock();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(array('campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false))
            ->andReturn([$user])
            ->once();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())->andReturn([])
            ->andReturn([])
            ->once();
        $this->userManager->shouldReceive('update')->with($user, false)->once();
        $update = m::mock('Ilios\CoreBundle\Entity\PendingUserUpdate')
            ->shouldReceive('setType')->with('emailMismatch')->once()
            ->shouldReceive('setProperty')->with('email')->once()
            ->shouldReceive('setValue')->with('new-email')->once()
            ->shouldReceive('setUser')->with($user)->once()
            ->mock();
        $this->pendingUserUpdateManager->shouldReceive('create')->andReturn($update)->once();
        $this->pendingUserUpdateManager->shouldReceive('update')->with($update, false)->once();
                
        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Completed sync process 1 users found in the directory; 0 users updated./',
            $output
        );
    }

    public function testExecuteWithIgnoredAuthenticationWithSameUserName()
    {
        $this->userManager->shouldReceive('getAllCampusIds')
            ->with(false, false)->andReturn(['abc']);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
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
        $this->userManager
            ->shouldReceive('findBy')
            ->with(array('campusId' => 'abc', 'enabled' => true, 'userSyncIgnore' => false))
            ->andReturn([$user1])
            ->once();
        $this->userManager->shouldReceive('update')->with($user1, false)->once();
        $this->userManager
            ->shouldReceive('findBy')
            ->with(m::hasKey('examined'), m::any())
            ->andReturn([])
            ->once();
        $this->authenticationManager->shouldReceive('findOneBy')
            ->with(['username' => 'abc123'])->once()->andReturn($duplicateAuthentication);

        $this->em->shouldReceive('flush')->twice();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));


        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/\[E\] There is already an account for username abc123 belonging to user #11 with Campus ID abc/',
            $output
        );
        $this->assertRegExp(
            '/Completed sync process 1 users found in the directory; 0 users updated./',
            $output
        );
    }
}
