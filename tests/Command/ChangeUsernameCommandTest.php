<?php
namespace App\Tests\Command;

use App\Command\ChangeUsernameCommand;
use App\Entity\AuthenticationInterface;
use App\Entity\Manager\AuthenticationManager;
use App\Entity\Manager\UserManager;
use App\Entity\UserInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

class ChangeUsernameCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:change-username';

    /** @var CommandTester */
    protected $commandTester;

    /** @var m\Mock */
    protected $userManager;
    /** @var m\Mock */
    protected $authenticationManager;

    public function setUp()
    {
        $this->userManager = m::mock(UserManager::class);
        $this->authenticationManager = m::mock(AuthenticationManager::class);

        $command = new ChangeUsernameCommand(
            $this->userManager,
            $this->authenticationManager
        );
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * Remove all mock objects
     */
    public function tearDown()
    {
        unset($this->userManager);
        unset($this->authenticationManager);
        unset($this->commandTester);
    }
    
    public function testChangeUsername()
    {
        $user = m::mock(UserInterface::class);
        $this->userManager->shouldReceive('findOneBy')->with(array('id' => 1))->andReturn($user);
        $this->commandTester->setInputs(['newname']);

        $authentication = m::mock(AuthenticationInterface::class);
        $user->shouldReceive('getAuthentication')->once()->andReturn($authentication);
        $this->authenticationManager->shouldReceive('getUsernames')->once()->andReturn([]);

        $authentication->shouldReceive('setUsername')->with('newname')->once();
        $this->authenticationManager->shouldReceive('update')->with($authentication);

        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Username Changed/',
            $output
        );
    }

    public function testUserWithoutAuthentication()
    {
        $user = m::mock(UserInterface::class);
        $this->userManager->shouldReceive('findOneBy')->with(array('id' => 1))->andReturn($user);
        $this->commandTester->setInputs(['newname']);

        $authentication = m::mock(AuthenticationInterface::class);
        $user->shouldReceive('getAuthentication')->once()->andReturn(null);
        $this->authenticationManager->shouldReceive('create')->once()->andReturn($authentication);
        $user->shouldReceive('setAuthentication')->once()->with($authentication);
        $this->authenticationManager->shouldReceive('getUsernames')->once()->andReturn([]);

        $authentication->shouldReceive('setUsername')->with('newname')->once();
        $this->authenticationManager->shouldReceive('update')->with($authentication);

        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ));


        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Username Changed/',
            $output
        );
    }

    public function testDuplicationUsername()
    {
        $user = m::mock(UserInterface::class);
        $this->userManager->shouldReceive('findOneBy')->with(array('id' => 1))->andReturn($user);
        $this->commandTester->setInputs(['newname']);

        $this->authenticationManager->shouldReceive('getUsernames')->once()->andReturn(['newname']);

        $this->expectException(\RuntimeException::class);
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ));
    }

    public function testDuplicateUsernameCase()
    {
        $user = m::mock(UserInterface::class);
        $this->userManager->shouldReceive('findOneBy')->with(array('id' => 1))->andReturn($user);
        $this->commandTester->setInputs(['newName']);

        $this->authenticationManager->shouldReceive('getUsernames')->once()->andReturn(['newname']);

        $this->expectException(\RuntimeException::class);
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ));
    }

    public function testDuplicateUsernameCaseInDB()
    {
        $user = m::mock(UserInterface::class);
        $this->userManager->shouldReceive('findOneBy')->with(array('id' => 1))->andReturn($user);
        $this->commandTester->setInputs(['newname']);

        $this->authenticationManager->shouldReceive('getUsernames')->once()->andReturn(['newName']);

        $this->expectException(\RuntimeException::class);
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ));
    }

    public function testCaseIsPreserved()
    {
        $user = m::mock(UserInterface::class);
        $this->userManager->shouldReceive('findOneBy')->with(array('id' => 1))->andReturn($user);
        $this->commandTester->setInputs(['NewName']);

        $authentication = m::mock(AuthenticationInterface::class);
        $user->shouldReceive('getAuthentication')->once()->andReturn(null);
        $this->authenticationManager->shouldReceive('create')->once()->andReturn($authentication);
        $user->shouldReceive('setAuthentication')->once()->with($authentication);
        $this->authenticationManager->shouldReceive('getUsernames')->once()->andReturn([]);

        $authentication->shouldReceive('setUsername')->with('NewName')->once();
        $this->authenticationManager->shouldReceive('update')->with($authentication);

        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ));


        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Username Changed/',
            $output
        );
    }

    public function testWhitespaceIsTrimmed()
    {
        $user = m::mock(UserInterface::class);
        $this->userManager->shouldReceive('findOneBy')->with(array('id' => 1))->andReturn($user);
        $this->commandTester->setInputs(['  username  ']);

        $authentication = m::mock(AuthenticationInterface::class);
        $user->shouldReceive('getAuthentication')->once()->andReturn(null);
        $this->authenticationManager->shouldReceive('create')->once()->andReturn($authentication);
        $user->shouldReceive('setAuthentication')->once()->with($authentication);
        $this->authenticationManager->shouldReceive('getUsernames')->once()->andReturn([]);

        $authentication->shouldReceive('setUsername')->with('username')->once();
        $this->authenticationManager->shouldReceive('update')->with($authentication);

        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ));


        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Username Changed/',
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
