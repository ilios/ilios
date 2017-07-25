<?php

namespace Tests\CliBundle\Command;

use Ilios\CliBundle\Command\InvalidateUserTokenCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use \DateTime;
use PHPUnit\Framework\TestCase;

/**
 * Class InvalidateUserTokenCommandTest
 */
class InvalidateUserTokenCommandTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:maintenance:invalidate-user-tokens';
    
    protected $userManager;
    protected $authenticationManager;
    protected $commandTester;
    
    public function setUp()
    {
        $this->userManager = m::mock('Ilios\CoreBundle\Entity\Manager\UserManager');
        $this->authenticationManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManager');
        
        $command = new InvalidateUserTokenCommand($this->userManager, $this->authenticationManager);
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
        unset($this->authenticationManager);
        unset($this->commandTester);
    }
    
    public function testHappyPathExecute()
    {
        $now = new DateTime();
        sleep(2);
        $authentication = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('setInvalidateTokenIssuedBefore')->mock();
        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface')
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
        $authentication = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('setUser')
            ->shouldReceive('setInvalidateTokenIssuedBefore')
            ->mock();
        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface')
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
