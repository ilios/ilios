<?php
namespace Tests\CliBundle\Command;

use Ilios\CliBundle\Command\InstallFirstUserCommand;
use Ilios\CoreBundle\Entity\Manager\AuthenticationManager;
use Ilios\CoreBundle\Entity\Manager\SchoolManager;
use Ilios\CoreBundle\Entity\Manager\UserManager;
use Ilios\CoreBundle\Entity\Manager\UserRoleManager;
use Ilios\CoreBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

use Mockery as m;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class InstallFirstUserCommandTest
 */
class InstallFirstUserCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
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
        $this->userManager = m::mock(UserManager::class);
        $this->authenticationManager = m::mock(AuthenticationManager::class);
        $this->schoolManager = m::mock(SchoolManager::class);
        $this->userRoleManager = m::mock(UserRoleManager::class);
        $this->encoder = m::mock(UserPasswordEncoderInterface::class);

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
        unset($this->questionHelper);
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
        $this->expectException(\Exception::class, 'Sorry, at least one user record already exists.');
        $this->commandTester->execute(array(
            'command' => self::COMMAND_NAME,
            '--school' => '1'
        ));
    }

    public function testBadSchoolId()
    {
        $this->userManager->shouldReceive('findOneBy')->with([])->andReturn(null);
        $this->schoolManager->shouldReceive('findBy')->with([], ['title' => 'ASC'])->andReturn([]);
        $this->expectException(\Exception::class, 'No schools found.');
        $this->commandTester->execute(array(
            'command' => self::COMMAND_NAME,
            '--school' => '1'
        ));
    }

    public function testAskForMissingSchools()
    {
        $this->getReadyForInput();
        $this->commandTester->setInputs(['0', 'Yes']);
        $this->commandTester->execute(array(
            'command' => self::COMMAND_NAME,
            '--email' => 'email@example.com',
        ));
        $this->checkOuput();
    }

    public function testAskForMissingEmail()
    {
        $this->getReadyForInput();
        $this->commandTester->setInputs(['email@example.com', 'Yes']);
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
        $sessionUser = m::mock('Ilios\AuthenticationBundle\Classes\SessionUserInterface');
        $authentication = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('setUsername')->with('first_user')
            ->shouldReceive('setPasswordBcrypt')->with('hashBlurb')
            ->shouldReceive('getSessionUser')->andReturn($sessionUser)
            ->shouldReceive('setUser')
            ->mock();
        $developerRole = m::mock('Ilios\CoreBundle\Entity\UserRoleInterface');
        $courseDirectorRole = m::mock('Ilios\CoreBundle\Entity\UserRoleInterface');
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
            ->shouldReceive('addRole')->with($developerRole)
            ->shouldReceive('addRole')->with($courseDirectorRole)
            ->mock();
        $this->schoolManager->shouldReceive('findOneBy')->with(['id' => '1'])->andReturn($school);
        $this->schoolManager->shouldReceive('findBy')->with([], ['title' => 'ASC'])->andReturn([$school]);
        $this->userRoleManager
            ->shouldReceive('findOneBy')
            ->with(['title' => 'Developer'])
            ->andReturn($developerRole);
        $this->userRoleManager
            ->shouldReceive('findOneBy')
            ->with(['title' => 'Course Director'])
            ->andReturn($courseDirectorRole);
        $this->userManager->shouldReceive('findOneBy')->with([])->andReturn([]);
        $this->userManager->shouldReceive('create')->andReturn($user);
        $this->userManager->shouldReceive('update')->with($user);
        $this->authenticationManager->shouldReceive('create')->andReturn($authentication);
        $this->authenticationManager->shouldReceive('update')->with($authentication);
        $this->encoder->shouldReceive('encodePassword')->with($sessionUser, 'Ch4nge_m3')->andReturn('hashBlurb');
        $this->userManager->shouldReceive('update');
    }

    protected function checkOuput()
    {
        $output = $this->commandTester->getDisplay();
        $this->assertContains('Success!', $output);
    }
}
