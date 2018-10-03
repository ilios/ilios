<?php

namespace App\Tests\Command;

use App\Command\InvalidateUserTokenCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use \DateTime;

/**
 * Class InvalidateUserTokenCommandTest
 */
class InvalidateUserTokenCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:invalidate-user-tokens';
    
    protected $userManager;
    protected $authenticationManager;
    protected $commandTester;
    
    public function setUp()
    {
        $this->userManager = m::mock('App\Entity\Manager\UserManager');
        $this->authenticationManager = m::mock('App\Entity\Manager\AuthenticationManager');
        
        $command = new InvalidateUserTokenCommand($this->userManager, $this->authenticationManager);
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
    
    public function testHappyPathExecute()
    {
        $now = new DateTime();
        sleep(2);
        $authentication = m::mock('App\Entity\AuthenticationInterface')
            ->shouldReceive('setInvalidateTokenIssuedBefore')->mock();
        $user = m::mock('App\Entity\UserInterface')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('getFirstAndLastName')->andReturn('somebody great')
            ->mock();
        $this->userManager->shouldReceive('findOneBy')->with(array('id' => 1))->andReturn($user);
        $this->authenticationManager->shouldReceive('update')->with($authentication);
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ));
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/All the tokens for somebody great issued before Today at [0-9:APM\s]+ UTC have been invalidated./',
            $output
        );
        
        preg_match('/[0-9:APM\s]+ UTC/', $output, $matches);
        $time = trim($matches[0]);
        $since = new DateTime($time);
        $diff = $since->getTimestamp() - $now->getTimestamp();
        $this->assertTrue(
            $diff > 1
        );
    }
    
    public function testNoAuthenticationForUser()
    {
        $authentication = m::mock('App\Entity\AuthenticationInterface')
            ->shouldReceive('setUser')
            ->shouldReceive('setInvalidateTokenIssuedBefore')
            ->mock();
        $user = m::mock('App\Entity\UserInterface')
            ->shouldReceive('getAuthentication')->andReturn(null)
            ->shouldReceive('getFirstAndLastName')->andReturn('somebody great')
            ->mock();
        $this->userManager->shouldReceive('findOneBy')->with(array('id' => 1))->andReturn($user);
        $this->authenticationManager
            ->shouldReceive('create')->andReturn($authentication)
            ->shouldReceive('update')->with($authentication);
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ));
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/All the tokens for somebody great issued before Today at [0-9:APM\s]+ UTC have been invalidated./',
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
