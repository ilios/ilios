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
    protected $learningMaterialManager;
    protected $commandTester;

    public function setUp()
    {
        $this->purifier = m::mock('HTMLPurifier');
        $this->objectiveManager = m::mock('Ilios\CoreBundle\Entity\Manager\ObjectiveManager');
        $this->learningMaterialManager = m::mock('Ilios\CoreBundle\Entity\Manager\LearningMaterialManager');
        $this->em = m::mock('Doctrine\Orm\EntityManager');

        $command = new CleanupStringsCommand(
            $this->purifier,
            $this->em,
            $this->objectiveManager,
            $this->learningMaterialManager
        );
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
        unset($this->learningMaterialManager);
        unset($this->commandTester);
        m::close();
    }
    
    public function testObjectiveTitle()
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

    public function testLearningMaterialDerscription()
    {
        $clean = m::mock('Ilios\CoreBundle\Entity\LearningMaterialInterface')
            ->shouldReceive('getDescription')->andReturn('clean title')
            ->mock();
        $dirty = m::mock('Ilios\CoreBundle\Entity\LearningMaterialInterface')
            ->shouldReceive('getDescription')->andReturn('<script>alert();</script><h1>html title</h1>')
            ->shouldReceive('setDescription')->with('<h1>html title</h1>')
            ->mock();
        $this->learningMaterialManager->shouldReceive('findLearningMaterialsBy')->with(array(), array('id' => 'ASC'), 500, 1)
            ->andReturn(array($clean, $dirty));
        $this->learningMaterialManager->shouldReceive('updateLearningMaterial')->with($dirty, false);
        $this->learningMaterialManager->shouldReceive('getTotalLearningMaterialCount')->andReturn(2);

        $this->purifier->shouldReceive('purify')->with('clean title')->andReturn('clean title');
        $this->purifier->shouldReceive('purify')
            ->with('<script>alert();</script><h1>html title</h1>')
            ->andReturn('<h1>html title</h1>');
        $this->em->shouldReceive('flush')->once();
        $this->em->shouldReceive('clear')->once();
        $this->commandTester->execute(array(
            'command'           => self::COMMAND_NAME,
            '--learningmaterial-description' => true
        ));


        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/1 Learning Material Descriptions updated/',
            $output
        );
    }
}
