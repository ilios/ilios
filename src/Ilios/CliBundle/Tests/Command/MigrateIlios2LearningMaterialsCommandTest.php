<?php
namespace Ilios\CliBundle\Tests\Command;

use Ilios\CliBundle\Command\MigrateIlios2LearningMaterialsCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

class MigrateIlios2LearningMaterialsCommandTest extends \PHPUnit_Framework_TestCase
{
    const COMMAND_NAME = 'ilios:setup:migrate-learning-materials';
    
    protected $symfonyFileSystem;
    protected $iliosFileSystem;
    protected $learningMaterialManager;
    
    public function setUp()
    {
        $this->symfonyFileSystem = m::mock('Symfony\Component\Filesystem\Filesystem');
        $this->iliosFileSystem = m::mock('Ilios\CoreBundle\Classes\IliosFileSystem');
        $this->learningMaterialManager = m::mock('Ilios\CoreBundle\Entity\Manager\LearningMaterialManagerInterface');

        $command = new MigrateIlios2LearningMaterialsCommand(
            $this->symfonyFileSystem,
            $this->iliosFileSystem,
            $this->learningMaterialManager
        );
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
        unset($this->symfonyFileSystem);
        unset($this->iliosFileSystem);
        unset($this->directory);
        unset($this->learningMaterialManager);
        m::close();
    }
    
    public function testExecute()
    {
        $this->symfonyFileSystem
            ->shouldReceive('exists')->with('path')->andReturn(true)
            ->shouldReceive('exists')->with('path/pathtofile')->andReturn(true);
        $lm = m::mock('Ilios\CoreBundle\Entity\LearningMaterial')
            ->shouldReceive('getRelativePath')->andReturn('/pathtofile')->once()
            ->shouldReceive('setRelativePath')->with('newrelativepath')->once()
            ->mock();
        $this->learningMaterialManager
            ->shouldReceive('getTotalFileLearningMaterialCount')->andReturn(1)->once()
            ->shouldReceive('findFileLearningMaterials')->andReturn([$lm])->once()
            ->shouldReceive('updateLearningMaterial')->with($lm, false)->once()
            ->shouldReceive('flushAndClear')->once()
        ;
        $file = m::mock('Symfony\Component\HttpFoundation\File\File');
        
        $this->iliosFileSystem
            ->shouldReceive('getSymfonyFileForPath')->with('path/pathtofile')->andReturn($file)->once()
            ->shouldReceive('storeLearningMaterialFile')->with($file)->andReturn('newrelativepath')->once()
        ;
        $this->sayYesWhenAsked();
        
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'pathToIlios2'         => 'path'
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Ready to copy 1 learning materials. Shall we continue?/',
            $output
        );
        $this->assertRegExp(
            '/Migrated 1 learning materials successfully!/',
            $output
        );
    }
    
    public function testExecuteWithBadRelativePath()
    {
        $this->symfonyFileSystem
            ->shouldReceive('exists')->with('path')->andReturn(true)
            ->shouldReceive('exists')->with('path/pathtofile')->andReturn(false);
        
        $lm = m::mock('Ilios\CoreBundle\Entity\LearningMaterial')
            ->shouldReceive('getRelativePath')->andReturn('/pathtofile')->once()
            ->mock();
        $this->learningMaterialManager
            ->shouldReceive('getTotalFileLearningMaterialCount')->andReturn(1)->once()
            ->shouldReceive('findFileLearningMaterials')->andReturn([$lm])->once()
            ->shouldReceive('flushAndClear')->once()
        ;

        $this->sayYesWhenAsked();
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'pathToIlios2'         => 'path'
        ));

        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Migrated 0 learning materials successfully!/',
            $output
        );
        $this->assertRegExp(
            '/Skipped 1 learning materials because they could not be located or were already migrated./',
            $output
        );
        
    }
    
    public function testBadIlios2Path()
    {
        $this->symfonyFileSystem->shouldReceive('exists')->with('badpath')->andReturn(false);
        $this->setExpectedException('Exception', "'badpath' does not exist");
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'pathToIlios2'         => 'badpath'
        ));
    }
    
    public function testIlios2PathRequired()
    {
        $this->setExpectedException('RuntimeException');
        $this->commandTester->execute(array('command' => self::COMMAND_NAME));
    }

    protected function sayYesWhenAsked()
    {
        $stream = fopen('php://memory', 'r+', false);
        
        fputs($stream, 'Yes\\n');
        rewind($stream);
        
        $this->questionHelper->setInputStream($stream);
    }
}
