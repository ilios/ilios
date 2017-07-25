<?php
namespace Tests\CliBundle\Command;

use Ilios\CliBundle\Command\SyncUserCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use PHPUnit\Framework\TestCase;

/**
 * Class SyncUserCommandTest
 */
class SyncUserCommandTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:directory:sync-user';
    
    protected $userManager;
    protected $authenticationManager;
    protected $commandTester;
    protected $questionHelper;
    protected $directory;
    
    public function setUp()
    {
        $this->userManager = m::mock('Ilios\CoreBundle\Entity\Manager\UserManager');
        $this->authenticationManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManager');
        $this->directory = m::mock('Ilios\CoreBundle\Service\Directory');
        
        $command = new SyncUserCommand($this->userManager, $this->authenticationManager, $this->directory);
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
        unset($this->directory);
        unset($this->commandTester);
    }
    
    public function testExecute()
    {
        $authentication = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('setUsername')->with('username')
            ->mock();
        $user = m::mock('Ilios\CoreBundle\Entity\UserInterface')
            ->shouldReceive('getFirstName')->andReturn('old-first')
            ->shouldReceive('getLastName')->andReturn('old-last')
            ->shouldReceive('getEmail')->andReturn('old-email')
            ->shouldReceive('getPhone')->andReturn('old-phone')
            ->shouldReceive('getCampusId')->andReturn('abc')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('setFirstName')->with('first')
            ->shouldReceive('setLastName')->with('last')
            ->shouldReceive('setEmail')->with('email')
            ->shouldReceive('setPhone')->with('phone')
            ->mock();
        $this->userManager->shouldReceive('findOneBy')->with(array('id' => 1))->andReturn($user);
        $this->userManager->shouldReceive('update')->with($user);
        $this->authenticationManager->shouldReceive('update')->with($authentication, false);
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'username' => 'username'
        ];
        $this->directory->shouldReceive('findByCampusId')->with('abc')->andReturn($fakeDirectoryUser);
        $this->commandTester->setInputs(['Yes']);
        
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Ilios User\s+\| abc\s+\| old-first\s+\| old-last\s+\| old-email\s+\| old-phone/',
            $output
        );
        $this->assertRegExp(
            '/Directory User\s+\| abc\s+\| first\s+\| last\s+\| email\s+\| phone/',
            $output
        );
    }
    
    public function testBadUserId()
    {
        $this->userManager->shouldReceive('findOneBy')->with(array('id' => 1))->andReturn(null);
        $this->expectException(\Exception::class, 'No user with id #1');
        $this->commandTester->execute(array(
            'command' => self::COMMAND_NAME,
            'userId' => '1'
        ));
    }
    
    public function testUserRequired()
    {
        $this->expectException(\RuntimeException::class);
        $this->commandTester->execute(array('command' => self::COMMAND_NAME));
    }
}
