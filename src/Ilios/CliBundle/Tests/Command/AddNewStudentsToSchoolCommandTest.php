<?php
namespace Ilios\CliBundle\Tests\Command;

use Ilios\CliBundle\Command\AddNewStudentsToSchoolCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

use Mockery as m;

/**
 * Class AddNewStudentsToSchoolCommandTest
 * @package Ilios\CliBundle\Tests\Command
 */
class AddNewStudentsToSchoolCommandTest extends \PHPUnit_Framework_TestCase
{
    const COMMAND_NAME = 'ilios:directory:add-students';
    
    protected $userManager;
    protected $userRoleManager;
    protected $schoolManager;
    protected $authenticationManager;
    protected $commandTester;
    protected $questionHelper;
    protected $directory;
    
    public function setUp()
    {
        $this->userManager = m::mock('Ilios\CoreBundle\Entity\Manager\UserManagerInterface');
        $this->userRoleManager = m::mock('Ilios\CoreBundle\Entity\Manager\UserRoleManagerInterface');
        $this->schoolManager = m::mock('Ilios\CoreBundle\Entity\Manager\SchoolManagerInterface');
        $this->authenticationManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManager');
        $this->directory = m::mock('Ilios\CoreBundle\Service\Directory');
        
        $command = new AddNewStudentsToSchoolCommand(
            $this->userManager,
            $this->schoolManager,
            $this->authenticationManager,
            $this->userRoleManager,
            $this->directory
        );
        $application = new Application();
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
        $this->questionHelper = $command->getHelper('question');
        
    }

    /**
     * Remove all mock objects
     */
    public function tearDown()
    {
        unset($this->userManager);
        unset($this->userRoleManager);
        unset($this->schoolManager);
        unset($this->authenticationManager);
        unset($this->directory);
        unset($this->commandTester);
        m::close();
    }
    
    public function testExecute()
    {
        $fakeDirectoryUser1 = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'username',
        ];
        $fakeDirectoryUser2 = [
            'firstName' => 'first2',
            'lastName' => 'last2',
            'email' => 'email2',
            'telephoneNumber' => 'phone2',
            'campusId' => 'abc2',
            'username' => 'username2',
        ];
        $school = m::mock('Ilios\CoreBundle\Entity\SchoolInterface')
            ->shouldReceive('getTitle')->andReturn('school 1')
            ->mock();
        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface')
            ->shouldReceive('getId')->andReturn(42)
            ->shouldReceive('getFirstName')->andReturn('first')
            ->shouldReceive('getLastName')->andReturn('last')
            ->shouldReceive('getFirstAndLastName')->andReturn('first last')
            ->shouldReceive('getEmail')->andReturn('email')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('setFirstName')->with('first')
            ->shouldReceive('setLastName')->with('last')
            ->shouldReceive('setEmail')->with('email')
            ->shouldReceive('setPhone')->with('phone')
            ->shouldReceive('setCampusId')->with('abc')
            ->shouldReceive('setAddedViaIlios')->with(true)
            ->shouldReceive('setEnabled')->with(true)
            ->shouldReceive('setUserSyncIgnore')->with(false)
            ->shouldReceive('setSchool')->with($school)
            ->shouldReceive('addRole')->with($school)
            ->mock();
        $authentication = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('setUser')->with($user)
            ->shouldReceive('setUsername')->with('username')
            ->mock();
        $this->directory->shouldReceive('findByLdapFilter')
            ->with('FILTER')
            ->andReturn([$fakeDirectoryUser1, $fakeDirectoryUser2]);
        $this->userManager->shouldReceive('getAllCampusIds')
            ->andReturn(['abc2']);
            
        $this->userManager->shouldReceive('createUser')->andReturn($user);
        $this->userManager
            ->shouldReceive('updateUser')
            ->with($user)->once();
        $this->schoolManager->shouldReceive('findSchoolBy')->with(array('id' => 1))->andReturn($school);
        $role = m::mock('Ilios\CoreBundle\Entity\UserRoleInterface')
            ->shouldReceive('addUser')->with($user)
            ->mock();
        $user->shouldReceive('addRole')->with($role);
        $this->userRoleManager
            ->shouldReceive('findUserRoleBy')
            ->with(array('title' => 'Student'))
            ->andReturn($role);
        $this->userRoleManager
            ->shouldReceive('updateUserRole')
            ->with($role);

        $this->authenticationManager->shouldReceive('createAuthentication')->andReturn($authentication);
        $this->authenticationManager->shouldReceive('updateAuthentication')->with($authentication, false);
        
        $this->sayYesWhenAsked();
        $this->commandTester->execute(array(
            'command'   => self::COMMAND_NAME,
            'schoolId'    => 1,
            'filter'    => 'FILTER',
        ));
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Found 2 students in the directory./',
            $output
        );
        
        $this->assertRegExp(
            '/There are 1 new students to be added to school 1./',
            $output
        );
        $this->assertRegExp(
            '/Do you wish to add these students to school 1?/',
            $output
        );
        $this->assertRegExp(
            '/abc       \| first \| last \| email /',
            $output
        );
        $this->assertRegExp(
            '/Success! New student #42 first last created./',
            $output
        );
    }
    
    public function testBadSchoolId()
    {
        $this->schoolManager->shouldReceive('findSchoolBy')->with(array('id' => 1))->andReturn(null);
        $this->setExpectedException('Exception', 'School with id 1 could not be found.');
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'filter'         => 'FILTER',
            'schoolId'         => '1'
        ));
        
    }
    
    public function testFilterRequired()
    {
        $this->setExpectedException('RuntimeException');
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'schoolId'         => '1'
        ));
    }
    
    public function testSchoolRequired()
    {
        $this->setExpectedException('RuntimeException');
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'filter'         => '1',
        ));
    }
    

    protected function sayYesWhenAsked()
    {
        $stream = fopen('php://memory', 'r+', false);
        
        fputs($stream, 'Yes\\n');
        rewind($stream);
        
        $this->questionHelper->setInputStream($stream);
    }
}
