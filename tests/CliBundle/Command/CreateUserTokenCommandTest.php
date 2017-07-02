<?php
namespace Tests\CliBundle\Command;

use Ilios\CliBundle\Command\CreateUserTokenCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class CreateUserTokenCommandTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:maintenance:create-user-token';
    
    protected $userManager;
    protected $commandTester;
    
    public function setUp()
    {
        $this->userManager = m::mock('Ilios\CoreBundle\Entity\Manager\UserManager');
        $this->jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        
        $command = new CreateUserTokenCommand($this->userManager, $this->jwtManager);
        $application = new Application();
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
        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface');
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
        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface');
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
