<?php
namespace App\Tests\Command;

use App\Command\FindUserCommand;
use App\Service\Directory;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

class FindUserCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:directory:find-user';
    
    protected $commandTester;
    protected $directory;
    
    public function setUp()
    {
        $this->directory = m::mock(Directory::class);
        $command = new FindUserCommand($this->directory);
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
