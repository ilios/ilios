<?php
namespace App\Tests\Command;

use App\Command\CreateUserTokenCommand;
use App\Service\JsonWebTokenManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

class CreateUserTokenCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:maintenance:create-user-token';
    
    protected $userManager;
    protected $commandTester;
    
    public function setUp()
    {
        $this->userManager = m::mock('App\Entity\Manager\UserManager');
        $this->jwtManager = m::mock(JsonWebTokenManager::class);
        
        $command = new CreateUserTokenCommand($this->userManager, $this->jwtManager);
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
        unset($this->commandTester);
    }
    
    public function testNewDefaultToken()
    {
        $user = m::mock('App\Entity\UserInterface');
        $this->userManager->shouldReceive('findOneBy')->with(array('id' => 1))->andReturn($user);
        $this->jwtManager->shouldReceive('createJwtFromUser')->with($user, 'PT8H')->andReturn('123JWT');
        
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Token 123JWT/',
            $output
        );
    }
    
    public function testNewTTLToken()
    {
        $user = m::mock('App\Entity\UserInterface');
        $this->userManager->shouldReceive('findOneBy')->with(array('id' => 1))->andReturn($user);
        $this->jwtManager->shouldReceive('createJwtFromUser')->with($user, '108Franks')->andReturn('123JWT');
        
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'userId'       => '1',
            '--ttl'        => '108Franks'
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Token 123JWT/',
            $output
        );
    }
    
    public function testBadUserId()
    {
        $this->userManager->shouldReceive('findOneBy')->with(array('id' => 1))->andReturn(null);
        $this->expectException(\Exception::class, 'No user with id #1');
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
