<?php
namespace Tests\CliBundle\Command;

use Ilios\CliBundle\Command\FindUserCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class FindUserCommandTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:directory:find-user';
    
    protected $commandTester;
    protected $directory;
    
    public function setUp()
    {
        $this->directory = m::mock('Ilios\CoreBundle\Service\Directory');
        $command = new FindUserCommand($this->directory);
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
        unset($this->directory);
        unset($this->commandTester);
    }
    
    public function testExecute()
    {
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
        ];
        $this->directory->shouldReceive('find')->with(array('a', 'b'))->andReturn(array($fakeDirectoryUser));
        
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'searchTerms'         => array('a', 'b')
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/abc\s+\| first\s+\| last\s+\| email\s+\| phone/',
            $output
        );
    }
    
    public function testTermRequired()
    {
        $this->expectException(\RuntimeException::class);
        $this->commandTester->execute(array('command' => self::COMMAND_NAME));
    }
}
