<?php
namespace App\Tests\Command;

use App\Command\MigrateIlios2LearningMaterialsCommand;
use App\Entity\LearningMaterialInterface;
use App\Service\IliosFileSystem;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use Symfony\Component\HttpFoundation\File\File;

class MigrateIlios2LearningMaterialsCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:migrate-learning-materials';
    
    protected $symfonyFileSystem;
    protected $iliosFileSystem;
    protected $learningMaterialManager;
    
    public function setUp()
    {
        $this->symfonyFileSystem = m::mock('Symfony\Component\Filesystem\Filesystem');
        $this->iliosFileSystem = m::mock(IliosFileSystem::class);
        $this->learningMaterialManager = m::mock('App\Entity\Manager\LearningMaterialManager');

        $command = new MigrateIlios2LearningMaterialsCommand(
            $this->symfonyFileSystem,
            $this->iliosFileSystem,
            $this->learningMaterialManager
        );
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
        $this->questionHelper = $command->getHelper('question');
    }

    /**
     * Remove all mock objects
     */
    public function tearDown() : void
    {
        unset($this->symfonyFileSystem);
        unset($this->iliosFileSystem);
        unset($this->directory);
        unset($this->learningMaterialManager);
    }
    
    public function testExecute()
    {
        $this->symfonyFileSystem
            ->shouldReceive('exists')->with(__DIR__ . '/')->andReturn(true)
            ->shouldReceive('exists')->with(__FILE__)->andReturn(true);
        $lm = m::mock(LearningMaterialInterface::class)
            ->shouldReceive('getRelativePath')->andReturn(basename(__FILE__))->once()
            ->shouldReceive('setRelativePath')->with('newrelativepath')->once()
            ->mock();
        $this->learningMaterialManager
            ->shouldReceive('getTotalFileLearningMaterialCount')->andReturn(1)->once()
            ->shouldReceive('findFileLearningMaterials')->andReturn([$lm])->once()
            ->shouldReceive('update')->with($lm, false)->once()
            ->shouldReceive('flushAndClear')->once()
        ;

        $this->iliosFileSystem
            ->shouldReceive('storeLearningMaterialFile')->with(\Mockery::on(function ($argument) {
                if ($argument instanceof File && $argument->getRealPath() === __FILE__) {
                    return true;
                }

                return false;
            }))->andReturn('newrelativepath')->once()
        ;
        $this->commandTester->setInputs(['Yes']);
        
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'pathToIlios2'         => __DIR__ . '/'
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
        
        $lm = m::mock('App\Entity\LearningMaterial')
            ->shouldReceive('getRelativePath')->andReturn('/pathtofile')->once()
            ->mock();
        $this->learningMaterialManager
            ->shouldReceive('getTotalFileLearningMaterialCount')->andReturn(1)->once()
            ->shouldReceive('findFileLearningMaterials')->andReturn([$lm])->once()
            ->shouldReceive('flushAndClear')->once()
        ;

        $this->commandTester->setInputs(['Yes']);
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
        $this->expectException(\Exception::class, "'badpath' does not exist");
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            'pathToIlios2'         => 'badpath'
        ));
    }
    
    public function testIlios2PathRequired()
    {
        $this->expectException(\RuntimeException::class);
        $this->commandTester->execute(array('command' => self::COMMAND_NAME));
    }
}
