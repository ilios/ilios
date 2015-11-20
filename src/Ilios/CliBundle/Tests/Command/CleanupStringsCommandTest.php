<?php
namespace Ilios\CliBundle\Tests\Command;

use Ilios\CliBundle\Command\CleanupStringsCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

class CleanupStringsCommandTest extends \PHPUnit_Framework_TestCase
{
    const COMMAND_NAME = 'ilios:maintenance:cleanup-strings';
    
    protected $purifier;
    protected $em;
    protected $objectiveManager;
    protected $commandTester;

    public function setUp()
    {
        $this->purifier = m::mock('HTMLPurifier');
        $this->objectiveManager = m::mock('Ilios\CoreBundle\Entity\Manager\ObjectiveManager');
        $this->em = m::mock('Doctrine\Orm\EntityManager');

        $command = new CleanupStringsCommand($this->purifier, $this->em, $this->objectiveManager);
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
        unset($this->purifier);
        unset($this->em);
        unset($this->objectiveManager);
        unset($this->commandTester);
        m::close();
    }
    
    public function testExecute()
    {
        $cleanObjective = m::mock('Ilios\CoreBundle\Entity\ObjectiveInterface')
            ->shouldReceive('getTitle')->andReturn('clean title')
            ->mock();
        $dirtyObjective = m::mock('Ilios\CoreBundle\Entity\ObjectiveInterface')
            ->shouldReceive('getTitle')->andReturn('<script>alert();</script><h1>html title</h1>')
            ->shouldReceive('setTitle')->with('<h1>html title</h1>')
            ->mock();
        $this->objectiveManager->shouldReceive('findObjectivesBy')->with(array(), array('id' => 'ASC'), 500, 1)
            ->andReturn(array($cleanObjective, $dirtyObjective));
        $this->objectiveManager->shouldReceive('updateObjective')->with($dirtyObjective, false);
        $this->objectiveManager->shouldReceive('getTotalObjectiveCount')->andReturn(2);

        $this->purifier->shouldReceive('purify')->with('clean title')->andReturn('clean title');
        $this->purifier->shouldReceive('purify')
            ->with('<script>alert();</script><h1>html title</h1>')
            ->andReturn('<h1>html title</h1>');
        $this->em->shouldReceive('flush')->once();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'           => self::COMMAND_NAME,
            '--objective-title' => true
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/1 Objective Titles updated/',
            $output
        );
    }
}
