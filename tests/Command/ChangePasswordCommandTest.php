<?php
namespace App\Tests\Command;

use App\Classes\SessionUserInterface;
use App\Command\ChangePasswordCommand;
use App\Entity\AuthenticationInterface;
use App\Entity\Manager\AuthenticationManager;
use App\Entity\Manager\UserManager;
use App\Entity\UserInterface;
use App\Service\SessionUserProvider;
use App\Tests\Helper\TestQuestionHelper;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ChangePasswordCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:change-password';

    /** @var CommandTester */
    protected $commandTester;

    /** @var m\Mock */
    protected $userManager;
    /** @var m\Mock */
    protected $authenticationManager;
    /** @var m\Mock */
    protected $encoder;
    /** @var m\Mock */
    protected $sessionUserProvider;

    public function setUp()
    {
        $this->userManager = m::mock(UserManager::class);
        $this->authenticationManager = m::mock(AuthenticationManager::class);
        $this->encoder = m::mock(UserPasswordEncoderInterface::class);
        $this->sessionUserProvider = m::mock(SessionUserProvider::class);

        $command = new ChangePasswordCommand(
            $this->userManager,
            $this->authenticationManager,
            $this->encoder,
            $this->sessionUserProvider
        );
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);

        // Override the question helper to fix testing issue with hidden password input
        $helper = new TestQuestionHelper();
        $commandInApp->getHelperSet()->set($helper, 'question');
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * Remove all mock objects
     */
    public function tearDown() : void
    {
        unset($this->userManager);
        unset($this->authenticationManager);
        unset($this->encoder);
        unset($this->sessionUserProvider);
        unset($this->commandTester);
    }
    
    public function testChangePassword()
    {
        $user = m::mock(UserInterface::class);
        $this->userManager->shouldReceive('findOneBy')->with(array('id' => 1))->andReturn($user);
        $this->commandTester->setInputs(['123456789']);

        $authentication = m::mock(AuthenticationInterface::class);
        $user->shouldReceive('getAuthentication')->once()->andReturn($authentication);

        $sessionUser = m::mock(SessionUserInterface::class);
        $this->sessionUserProvider->shouldReceive('createSessionUserFromUser')->with($user)->andReturn($sessionUser);

        $this->encoder->shouldReceive('encodePassword')->with($sessionUser, '123456789')->andReturn('abc');
        $authentication->shouldReceive('setPasswordBcrypt')->with('abc')->once();

        $this->authenticationManager->shouldReceive('update')->with($authentication);

        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Password Changed/',
            $output
        );
    }

    public function testUserWithoutAuthentication()
    {
        $user = m::mock(UserInterface::class);
        $this->userManager->shouldReceive('findOneBy')->with(array('id' => 1))->andReturn($user);
        $this->commandTester->setInputs(['123456789']);

        $authentication = m::mock(AuthenticationInterface::class);
        $user->shouldReceive('getAuthentication')->once()->andReturn(null);
        $this->authenticationManager->shouldReceive('create')->once()->andReturn($authentication);
        $user->shouldReceive('setAuthentication')->once()->with($authentication);

        $sessionUser = m::mock(SessionUserInterface::class);
        $this->sessionUserProvider->shouldReceive('createSessionUserFromUser')->with($user)->andReturn($sessionUser);

        $this->encoder->shouldReceive('encodePassword')->with($sessionUser, '123456789')->andReturn('abc');
        $authentication->shouldReceive('setPasswordBcrypt')->with('abc')->once();

        $this->authenticationManager->shouldReceive('update')->with($authentication);

        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ));


        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Password Changed/',
            $output
        );
    }

    public function testBadUserId()
    {
        $this->userManager->shouldReceive('findOneBy')->with(array('id' => 1))->andReturn(null);
        $this->expectException(\Exception::class);
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ));
    }
    
    public function testUserRequired()
    {
        $this->expectException(\RuntimeException::class);
        $this->commandTester->execute(array('command' => self::COMMAND_NAME));
    }
}
