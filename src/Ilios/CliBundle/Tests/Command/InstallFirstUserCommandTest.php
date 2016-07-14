<?php
namespace Ilios\CliBundle\Tests\Command;

use Ilios\CliBundle\Command\InstallFirstUserCommand;
use Ilios\CoreBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

use Mockery as m;

/**
 * Class InstallFirstUserCommandTest
 * @package Ilios\CliBundle\Tests\Command
 */
class InstallFirstUserCommandTest extends KernelTestCase
{
    const COMMAND_NAME = 'ilios:setup:first-user';

    protected $userManager;
    protected $authenticationManager;
    protected $schoolManager;
    protected $userRoleManager;
    protected $encoder;
    protected $questionHelper;

    /**
     * @var CommandTester
     */
    protected $commandTester;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->userManager = m::mock('Ilios\CoreBundle\Entity\Manager\UserManager');
        $this->authenticationManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManager');
        $this->schoolManager = m::mock('Ilios\CoreBundle\Entity\Manager\SchoolManager');
        $this->userRoleManager = m::mock('Ilios\CoreBundle\Entity\Manager\BaseManager');
        $this->encoder = m::mock('Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface');


        $command = new InstallFirstUserCommand(
            $this->userManager,
            $this->schoolManager,
            $this->userRoleManager,
            $this->authenticationManager,
            $this->encoder
        );
        $kernel = $this->createKernel();
        $kernel->boot();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
        $this->questionHelper = $commandInApp->getHelper('question');
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        unset($this->userManager);
        unset($this->authenticationManager);
        unset($this->schoolManager);
        unset($this->userRoleManager);
        unset($this->commandTester);
        unset($this->formHelper);
        m::close();
    }

    public function testExecute()
    {
        $this->getReadyForInput();
        $this->commandTester->execute(array(
            'command'  => self::COMMAND_NAME,
            '--school' => '1',
            '--email' => 'email@example.com',
        ));

        $this->checkOuput();
    }

    public function testUserExists()
    {
        $this->userManager->shouldReceive('findOneBy')->with([])->andReturn(new User());
        $this->schoolManager->shouldReceive('findOneBy')->with(['id' => 1])->andReturn(null);
        $this->setExpectedException('Exception', 'Sorry, at least one user record already exists.');
        $this->commandTester->execute(array(
            'command' => self::COMMAND_NAME,
            '--school' => '1'
        ));
    }

    public function testBadSchoolId()
    {
        $this->userManager->shouldReceive('findOneBy')->with([])->andReturn(null);
        $this->schoolManager->shouldReceive('findBy')->with([], ['title' => 'ASC'])->andReturn([]);
        $this->setExpectedException('Exception', 'No schools found.');
        $this->commandTester->execute(array(
            'command' => self::COMMAND_NAME,
            '--school' => '1'
        ));
    }

    public function testAskForMissingSchools()
    {
        $this->getReadyForInput();
        $this->answerQuestion(['0', 'Yes']);
        $this->commandTester->execute(array(
            'command' => self::COMMAND_NAME,
            '--email' => 'email@example.com',
        ));
        $this->checkOuput();
    }

    public function testAskForMissingEmail()
    {
        $this->getReadyForInput();
        $this->answerQuestion(['email@example.com', 'Yes']);
        $this->commandTester->execute(array(
            'command' => self::COMMAND_NAME,
            '--school' => '1',
        ));
        $this->checkOuput();
    }

    protected function getReadyForInput()
    {
        $school = m::mock('Ilios\CoreBundle\Entity\SchoolInterface')
            ->shouldReceive('getId')->andReturn(1)
            ->shouldReceive('getTitle')->andReturn('Big School Title')
            ->mock();
        $authentication = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('setUsername')->with('first_user')
            ->shouldReceive('setPasswordBcrypt')->with('hashBlurb')
            ->shouldReceive('setUser')
            ->mock();
        $userRole = m::mock('Ilios\CoreBundle\Entity\UserRoleInterface');
        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface')
            ->shouldReceive('setFirstName')->with('First')
            ->shouldReceive('setLastName')->with('User')
            ->shouldReceive('setMiddleName')
            ->shouldReceive('setEmail')->with('email@example.com')
            ->shouldReceive('setAddedViaIlios')->with(true)
            ->shouldReceive('setEnabled')->with(true)
            ->shouldReceive('setUserSyncIgnore')->with(false)
            ->shouldReceive('setSchool')->with($school)
            ->shouldReceive('setAuthentication')->with($authentication)
            ->shouldReceive('addRole')->with($userRole)
            ->mock();
        $this->schoolManager->shouldReceive('findOneBy')->with(['id' => '1'])->andReturn($school);
        $this->schoolManager->shouldReceive('findBy')->with([], ['title' => 'ASC'])->andReturn([$school]);
        $this->userRoleManager->shouldReceive('findOneBy')->with(['title' => 'Developer'])->andReturn($userRole);
        $this->userManager->shouldReceive('findOneBy')->with([])->andReturn([]);
        $this->userManager->shouldReceive('create')->andReturn($user);
        $this->userManager->shouldReceive('update')->with($user);
        $this->authenticationManager->shouldReceive('create')->andReturn($authentication);
        $this->authenticationManager->shouldReceive('update')->with($authentication);
        $this->encoder->shouldReceive('encodePassword')->with($user, 'Ch4nge_m3')->andReturn('hashBlurb');
        $this->userManager->shouldReceive('update');
    }

    protected function checkOuput()
    {
        $output = $this->commandTester->getDisplay();
        $this->assertContains('Success!', $output);
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
