<?php
namespace Ilios\CliBundle\Tests\Command;

use Ilios\CliBundle\Command\AddUserCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class AddUserCommandTest
 * @package Ilios\CliBundle\Tests\Command
 */
class AddUserCommandTest extends \PHPUnit_Framework_TestCase
{
    const COMMAND_NAME = 'ilios:maintenance:add-user';
    
    protected $userManager;
    protected $authenticationManager;
    protected $schoolManager;
    protected $encoder;
    protected $commandTester;
    protected $questionHelper;
    
    public function setUp()
    {
        $this->userManager = m::mock('Ilios\CoreBundle\Entity\Manager\UserManager');
        $this->authenticationManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManager');
        $this->schoolManager = m::mock('Ilios\CoreBundle\Entity\Manager\SchoolManager');
        $this->encoder = m::mock('Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface');


        $command = new AddUserCommand(
            $this->userManager,
            $this->authenticationManager,
            $this->schoolManager,
            $this->encoder
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
        unset($this->commandTester);
        unset($this->questionHelper);
        m::close();
    }
    
    public function testExecute()
    {
        $this->getReadyForInput();
        $this->answerQuestion(['Yes']);

        $this->commandTester->execute(array(
            'command'           => self::COMMAND_NAME,
            '--schoolId'          => '1',
            '--firstName'         => 'first',
            '--lastName'          => 'last',
            '--email'             => 'email@example.com',
            '--telephoneNumber'   => 'phone',
            '--campusId'          => 'abc',
            '--username'          => 'abc123',
            '--password'          => 'abc123pass',
        ));

        $this->checkOuput();
    }

    
    public function testBadSchoolId()
    {
        $this->userManager->shouldReceive('findOneBy')->with(array('campusId' => 1))->andReturn(null);
        $this->schoolManager->shouldReceive('findOneBy')->with(array('id' => 1))->andReturn(null);
        $this->setExpectedException('Exception', 'School with id 1 could not be found.');
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            '--schoolId'         => '1'
        ));
    }

    public function testAskForMissingFirstName()
    {
        $this->getReadyForInput();

        $this->answerQuestion(['first', 'Yes']);

        $this->commandTester->execute(array(
            'command'           => self::COMMAND_NAME,
            '--schoolId'          => '1',
            '--lastName'          => 'last',
            '--email'             => 'email@example.com',
            '--telephoneNumber'   => 'phone',
            '--campusId'          => 'abc',
            '--username'          => 'abc123',
            '--password'          => 'abc123pass',
        ));


        $this->checkOuput();
    }

    public function testAskForMissingLastName()
    {
        $this->getReadyForInput();

        $this->answerQuestion(['last', 'Yes']);

        $this->commandTester->execute(array(
            'command'             => self::COMMAND_NAME,
            '--schoolId'          => '1',
            '--firstName'         => 'first',
            '--email'             => 'email@example.com',
            '--telephoneNumber'   => 'phone',
            '--campusId'          => 'abc',
            '--username'          => 'abc123',
            '--password'          => 'abc123pass',
        ));


        $this->checkOuput();
    }

    public function testAskForMissingEmail()
    {
        $this->getReadyForInput();

        $this->answerQuestion(['email@example.com', 'Yes']);

        $this->commandTester->execute(array(
            'command'             => self::COMMAND_NAME,
            '--schoolId'          => '1',
            '--firstName'         => 'first',
            '--lastName'          => 'last',
            '--telephoneNumber'   => 'phone',
            '--campusId'          => 'abc',
            '--username'          => 'abc123',
            '--password'          => 'abc123pass',
        ));


        $this->checkOuput();
    }

    public function testAskForMissingPhone()
    {
        $this->getReadyForInput();

        $this->answerQuestion(['phone', 'Yes']);

        $this->commandTester->execute(array(
            'command'             => self::COMMAND_NAME,
            '--schoolId'          => '1',
            '--firstName'         => 'first',
            '--lastName'          => 'last',
            '--email'             => 'email@example.com',
            '--campusId'          => 'abc',
            '--username'          => 'abc123',
            '--password'          => 'abc123pass',
        ));


        $this->checkOuput();
    }

    public function testAskForMissingCampusId()
    {
        $this->getReadyForInput();

        $this->answerQuestion(['abc', 'Yes']);

        $this->commandTester->execute(array(
            'command'             => self::COMMAND_NAME,
            '--schoolId'          => '1',
            '--firstName'         => 'first',
            '--lastName'          => 'last',
            '--email'             => 'email@example.com',
            '--telephoneNumber'   => 'phone',
            '--username'          => 'abc123',
            '--password'          => 'abc123pass',
        ));


        $this->checkOuput();
    }

    public function testAskForMissingUsername()
    {
        $this->getReadyForInput();

        $this->answerQuestion(['abc123', 'Yes']);

        $this->commandTester->execute(array(
            'command'             => self::COMMAND_NAME,
            '--schoolId'          => '1',
            '--firstName'         => 'first',
            '--lastName'          => 'last',
            '--email'             => 'email@example.com',
            '--telephoneNumber'   => 'phone',
            '--campusId'          => 'abc',
            '--password'          => 'abc123pass',
        ));


        $this->checkOuput();
    }

    public function testAskForMissingPassword()
    {
        $this->getReadyForInput();

        $this->answerQuestion(['abc123pass', 'Yes']);

        $this->commandTester->execute(array(
            'command'             => self::COMMAND_NAME,
            '--schoolId'          => '1',
            '--firstName'         => 'first',
            '--lastName'          => 'last',
            '--email'             => 'email@example.com',
            '--telephoneNumber'   => 'phone',
            '--campusId'          => 'abc',
            '--username'          => 'abc123',
        ));


        $this->checkOuput();
    }


    protected function getReadyForInput()
    {
        $school = m::mock('Ilios\CoreBundle\Entity\SchoolInterface');
        $school->shouldReceive('getTitle')->andReturn('Big School Title');
        $authentication = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('setUsername')->with('abc123')
            ->shouldReceive('setPasswordBcrypt')->with('hashBlurb')
            ->mock();
        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface')
            ->shouldReceive('setFirstName')->with('first')
            ->shouldReceive('setLastName')->with('last')
            ->shouldReceive('setEmail')->with('email@example.com')
            ->shouldReceive('setPhone')->with('phone')
            ->shouldReceive('setCampusId')->with('abc')
            ->shouldReceive('setAddedViaIlios')->with(true)
            ->shouldReceive('setEnabled')->with(true)
            ->shouldReceive('setUserSyncIgnore')->with(false)
            ->shouldReceive('setSchool')->with($school)
            ->shouldReceive('getId')->andReturn(1)
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('getFirstAndLastName')->andReturn('Test Person')
            ->shouldReceive('setAuthentication')->with($authentication)
            ->mock();
        $this->encoder->shouldReceive('encodePassword')->with($user, 'abc123pass')->andReturn('hashBlurb');

        $this->userManager->shouldReceive('findOneBy')->with(array('campusId' => 'abc'))->andReturn(false);
        $this->userManager->shouldReceive('findOneBy')->with(array('email' => 'email@example.com'))->andReturn(false);
        $this->schoolManager->shouldReceive('findOneBy')->with(array('id' => 1))->andReturn($school);
        $this->userManager->shouldReceive('create')->andReturn($user);
        $this->userManager->shouldReceive('update')->with($user);
        $this->authenticationManager->shouldReceive('create')->andReturn($authentication);
        $this->authenticationManager->shouldReceive('update')->with($authentication);
    }

    protected function checkOuput()
    {
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/abc       \| first \| last \| email@example.com \| abc123   \| phone/',
            $output
        );
        $this->assertRegExp(
            '/Success! New user #1 Test Person created./',
            $output
        );
    }

    protected function answerQuestion(array $input)
    {
        $stream = fopen('php://memory', 'r+', false);
        foreach ($input as $value) {
            fputs($stream, $value . "\n");
        }
        rewind($stream);

        $this->questionHelper->setInputStream($stream);
    }
}
