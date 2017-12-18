<?php

namespace Tests\CliBundle\Command;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\AuthenticationBundle\Service\SessionUserProvider;
use Ilios\CliBundle\Command\AddUserCommand;
use Ilios\CoreBundle\Entity\Authentication;
use Ilios\CoreBundle\Entity\Manager\AuthenticationManager;
use Ilios\CoreBundle\Entity\Manager\SchoolManager;
use Ilios\CoreBundle\Entity\Manager\UserManager;
use Ilios\CoreBundle\Entity\User;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class AddUserCommandTest
 */
class AddUserCommandTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:maintenance:add-user';

    protected $userManager;
    protected $authenticationManager;
    protected $schoolManager;
    protected $encoder;
    protected $commandTester;
    protected $questionHelper;
    protected $sessionUserProvider;

    public function setUp()
    {
        $this->userManager = m::mock(UserManager::class);
        $this->authenticationManager = m::mock(AuthenticationManager::class);
        $this->schoolManager = m::mock(SchoolManager::class);
        $this->encoder = m::mock(UserPasswordEncoderInterface::class);
        $this->sessionUserProvider = m::mock(SessionUserProvider::class);


        $command = new AddUserCommand(
            $this->userManager,
            $this->authenticationManager,
            $this->schoolManager,
            $this->encoder,
            $this->sessionUserProvider
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
        unset($this->sessionUserProvider);
    }

    public function testExecute()
    {
        $this->getReadyForInput();
        $this->commandTester->setInputs(['Yes']);

        $this->commandTester->execute(
            array(
                'command' => self::COMMAND_NAME,
                '--schoolId' => '1',
                '--firstName' => 'first',
                '--lastName' => 'last',
                '--email' => 'email@example.com',
                '--telephoneNumber' => 'phone',
                '--campusId' => 'abc',
                '--username' => 'abc123',
                '--password' => 'abc123pass',
            )
        );

        $this->checkOuput();
    }


    public function testBadSchoolId()
    {
        $this->userManager->shouldReceive('findOneBy')->with(array('campusId' => 1))->andReturn(null);
        $this->schoolManager->shouldReceive('findOneBy')->with(array('id' => 1))->andReturn(null);
        $this->expectException(\Exception::class, 'School with id 1 could not be found.');
        $this->commandTester->execute(
            array(
                'command' => self::COMMAND_NAME,
                '--schoolId' => '1',
            )
        );
    }

    public function testAskForMissingFirstName()
    {
        $this->getReadyForInput();

        $this->commandTester->setInputs(['first', 'Yes']);

        $this->commandTester->execute(
            array(
                'command' => self::COMMAND_NAME,
                '--schoolId' => '1',
                '--lastName' => 'last',
                '--email' => 'email@example.com',
                '--telephoneNumber' => 'phone',
                '--campusId' => 'abc',
                '--username' => 'abc123',
                '--password' => 'abc123pass',
            )
        );

        $this->checkOuput();
    }

    public function testAskForMissingLastName()
    {
        $this->getReadyForInput();

        $this->commandTester->setInputs(['last', 'Yes']);

        $this->commandTester->execute(
            array(
                'command' => self::COMMAND_NAME,
                '--schoolId' => '1',
                '--firstName' => 'first',
                '--email' => 'email@example.com',
                '--telephoneNumber' => 'phone',
                '--campusId' => 'abc',
                '--username' => 'abc123',
                '--password' => 'abc123pass',
            )
        );

        $this->checkOuput();
    }

    public function testAskForMissingEmail()
    {
        $this->getReadyForInput();

        $this->commandTester->setInputs(['email@example.com', 'Yes']);

        $this->commandTester->execute(
            array(
                'command' => self::COMMAND_NAME,
                '--schoolId' => '1',
                '--firstName' => 'first',
                '--lastName' => 'last',
                '--telephoneNumber' => 'phone',
                '--campusId' => 'abc',
                '--username' => 'abc123',
                '--password' => 'abc123pass',
            )
        );

        $this->checkOuput();
    }

    public function testAskForMissingPhone()
    {
        $this->getReadyForInput();

        $this->commandTester->setInputs(['phone', 'Yes']);

        $this->commandTester->execute(
            array(
                'command' => self::COMMAND_NAME,
                '--schoolId' => '1',
                '--firstName' => 'first',
                '--lastName' => 'last',
                '--email' => 'email@example.com',
                '--campusId' => 'abc',
                '--username' => 'abc123',
                '--password' => 'abc123pass',
            )
        );

        $this->checkOuput();
    }

    public function testAskForMissingCampusId()
    {
        $this->getReadyForInput();

        $this->commandTester->setInputs(['abc', 'Yes']);

        $this->commandTester->execute(
            array(
                'command' => self::COMMAND_NAME,
                '--schoolId' => '1',
                '--firstName' => 'first',
                '--lastName' => 'last',
                '--email' => 'email@example.com',
                '--telephoneNumber' => 'phone',
                '--username' => 'abc123',
                '--password' => 'abc123pass',
            )
        );

        $this->checkOuput();
    }

    public function testAskForMissingUsername()
    {
        $this->getReadyForInput();

        $this->commandTester->setInputs(['abc123', 'Yes']);

        $this->commandTester->execute(
            array(
                'command' => self::COMMAND_NAME,
                '--schoolId' => '1',
                '--firstName' => 'first',
                '--lastName' => 'last',
                '--email' => 'email@example.com',
                '--telephoneNumber' => 'phone',
                '--campusId' => 'abc',
                '--password' => 'abc123pass',
            )
        );

        $this->checkOuput();
    }

    public function testAskForMissingPassword()
    {
        $this->getReadyForInput();

        $this->commandTester->setInputs(['abc123pass', 'Yes']);

        $this->commandTester->execute(
            array(
                'command' => self::COMMAND_NAME,
                '--schoolId' => '1',
                '--firstName' => 'first',
                '--lastName' => 'last',
                '--email' => 'email@example.com',
                '--telephoneNumber' => 'phone',
                '--campusId' => 'abc',
                '--username' => 'abc123',
            )
        );

        $this->checkOuput();
    }

    protected function getReadyForInput()
    {
        $school = m::mock('Ilios\CoreBundle\Entity\SchoolInterface');
        $school->shouldReceive('getTitle')->andReturn('Big School Title');
        $sessionUser = m::mock('Ilios\AuthenticationBundle\Classes\SessionUserInterface');
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
            ->shouldReceive('getFirstAndLastName')->andReturn('Test Person')
            ->mock();

        $authentication = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('setUsername')->with('abc123')
            ->shouldReceive('setPasswordBcrypt')->with('hashBlurb')
            ->shouldReceive('getUser')->andReturn($user)
            ->mock();

        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('setAuthentication')->with($authentication);

        $this->encoder->shouldReceive('encodePassword')->with($sessionUser, 'abc123pass')->andReturn('hashBlurb');

        $this->userManager->shouldReceive('findOneBy')->with(array('campusId' => 'abc'))->andReturn(false);
        $this->userManager->shouldReceive('findOneBy')->with(array('email' => 'email@example.com'))->andReturn(false);
        $this->schoolManager->shouldReceive('findOneBy')->with(array('id' => 1))->andReturn($school);
        $this->userManager->shouldReceive('create')->andReturn($user);
        $this->userManager->shouldReceive('update')->with($user);
        $this->authenticationManager->shouldReceive('create')->andReturn($authentication);
        $this->authenticationManager->shouldReceive('update')->with($authentication);
        $this->sessionUserProvider->shouldReceive('createSessionUserFromUser')->with($user)->andReturn($sessionUser);
    }

    protected function checkOuput()
    {
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/abc\s+\| first\s+\| last\s+\| email@example.com\s+\| abc123\s+\| phone/',
            $output
        );
        $this->assertRegExp(
            '/Success! New user #1 Test Person created./',
            $output
        );
    }
}
