<?php
namespace App\Tests\Command;

use App\Classes\SessionUserInterface;
use App\Service\SessionUserProvider;
use App\Command\InstallFirstUserCommand;
use App\Entity\Manager\AuthenticationManager;
use App\Entity\Manager\SchoolManager;
use App\Entity\Manager\UserManager;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

use Mockery as m;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class InstallFirstUserCommandTest
 */
class InstallFirstUserCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:setup-first-user';

    protected $userManager;
    protected $authenticationManager;
    protected $schoolManager;
    protected $encoder;
    protected $questionHelper;
    protected $sessionUserProvider;

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
        $this->encoder = m::mock(UserPasswordEncoderInterface::class);
        $this->sessionUserProvider = m::mock(SessionUserProvider::class);

        $command = new InstallFirstUserCommand(
            $this->userManager,
            $this->schoolManager,
            $this->authenticationManager,
            $this->encoder,
            $this->sessionUserProvider
        );
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
        $this->questionHelper = $commandInApp->getHelper('question');
    }

    /**
     * @inheritdoc
     */
    public function tearDown() : void
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
        $school = m::mock('App\Entity\SchoolInterface')
            ->shouldReceive('getId')->andReturn(1)
            ->shouldReceive('getTitle')->andReturn('Big School Title')
            ->mock();
        $sessionUser = m::mock(SessionUserInterface::class);
        $developerRole = m::mock('App\Entity\UserRoleInterface');
        $courseDirectorRole = m::mock('App\Entity\UserRoleInterface');
        $user = m::mock('App\Entity\UserInterface')
            ->shouldReceive('setFirstName')->with('First')
            ->shouldReceive('setLastName')->with('User')
            ->shouldReceive('setMiddleName')
            ->shouldReceive('setEmail')->with('email@example.com')
            ->shouldReceive('setAddedViaIlios')->with(true)
            ->shouldReceive('setEnabled')->with(true)
            ->shouldReceive('setUserSyncIgnore')->with(false)
            ->shouldReceive('setSchool')->with($school)
            ->shouldReceive('setRoot')->with(true)
            ->mock();
        $authentication = m::mock('App\Entity\AuthenticationInterface')
            ->shouldReceive('setUsername')->with('first_user')
            ->shouldReceive('setPasswordBcrypt')->with('hashBlurb')
            ->shouldReceive('getUser')->andReturn($user)
            ->shouldReceive('setUser')
            ->mock();
        $user->shouldReceive('setAuthentication')->with($authentication);
        $this->schoolManager->shouldReceive('findOneBy')->with(['id' => '1'])->andReturn($school);
        $this->schoolManager->shouldReceive('findBy')->with([], ['title' => 'ASC'])->andReturn([$school]);
        $this->userManager->shouldReceive('findOneBy')->with([])->andReturn([]);
        $this->userManager->shouldReceive('create')->andReturn($user);
        $this->userManager->shouldReceive('update')->with($user);
        $this->authenticationManager->shouldReceive('create')->andReturn($authentication);
        $this->authenticationManager->shouldReceive('update')->with($authentication);
        $this->encoder->shouldReceive('encodePassword')->with($sessionUser, 'Ch4nge_m3')->andReturn('hashBlurb');
        $this->userManager->shouldReceive('update');
        $this->sessionUserProvider->shouldReceive('createSessionUserFromUser')->with($user)->andReturn($sessionUser);
    }

    protected function checkOuput()
    {
        $output = $this->commandTester->getDisplay();
        $this->assertContains('Success!', $output);
    }
}
