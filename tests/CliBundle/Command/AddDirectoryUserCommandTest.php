<?php
namespace Tests\CliBundle\Command;

use Ilios\CliBundle\Command\AddDirectoryUserCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use PHPUnit\Framework\TestCase;

/**
 * Class AddDirectoryUserCommandTest
 */
class AddDirectoryUserCommandTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    const COMMAND_NAME = 'ilios:directory:add-user';
    
    protected $userManager;
    protected $authenticationManager;
    protected $schoolManager;
    protected $commandTester;
    protected $questionHelper;
    protected $directory;
    
    public function setUp()
    {
        $this->userManager = m::mock('Ilios\CoreBundle\Entity\Manager\UserManager');
        $this->authenticationManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManager');
        $this->schoolManager = m::mock('Ilios\CoreBundle\Entity\Manager\SchoolManager');
        $this->directory = m::mock('Ilios\CoreBundle\Service\Directory');

        $command = new AddDirectoryUserCommand(
            $this->userManager,
            $this->authenticationManager,
            $this->schoolManager,
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
        unset($this->authenticationManager);
        unset($this->schoolManager);
        unset($this->directory);
        unset($this->commandTester);
        unset($this->questionHelper);
    }
    
    public function testExecute()
    {
        $school = m::mock('Ilios\CoreBundle\Entity\SchoolInterface');
        $authentication = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('setUsername')->with('abc123')
            ->mock();
        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface')
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
        
        $this->userManager->shouldReceive('findOneBy')->with(array('campusId' => 'abc'))->andReturn(false);
        $this->schoolManager->shouldReceive('findOneBy')->with(array('id' => 1))->andReturn($school);
        $this->userManager->shouldReceive('create')->andReturn($user);
        $this->userManager->shouldReceive('update')->with($user);
        $this->authenticationManager->shouldReceive('create')->andReturn($authentication);
        $this->authenticationManager->shouldReceive('update')->with($authentication);
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
        
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'campusId'         => 'abc',
            'schoolId'         => '1',
        ));
        
        
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
        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface')
            ->shouldReceive('getId')->andReturn(1)
            ->mock();
        $this->userManager->shouldReceive('findOneBy')->with(array('campusId' => 1))->andReturn($user);
        $this->expectException(\Exception::class, 'User #1 with campus id 1 already exists.');
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'campusId'         => '1',
            'schoolId'         => '1'
        ));
    }
    
    public function testBadSchoolId()
    {
        $this->userManager->shouldReceive('findOneBy')->with(array('campusId' => 1))->andReturn(null);
        $this->schoolManager->shouldReceive('findOneBy')->with(array('id' => 1))->andReturn(null);
        $this->expectException(\Exception::class, 'School with id 1 could not be found.');
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'campusId'         => '1',
            'schoolId'         => '1'
        ));
    }
    
    public function testUserRequired()
    {
        $this->expectException(\RuntimeException::class);
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'schoolId'         => '1'
        ));
    }
    
    public function testSchoolRequired()
    {
        $this->expectException(\RuntimeException::class);
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'campusId'         => '1',
        ));
    }
}
