<?php
namespace Tests\CliBundle\Command;

use Ilios\CliBundle\Command\ValidateLearningMaterialPathsCommand;
use Ilios\CoreBundle\Service\IliosFileSystem;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class ValidateLearningMaterialPathsCommandTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:maintenance:validate-learning-materials';
    
    protected $iliosFileSystem;
    protected $learningMaterialManager;
    protected $commandTester;
    
    public function setUp()
    {
        $this->iliosFileSystem = m::mock(IliosFileSystem::class);
        $this->learningMaterialManager = m::mock('Ilios\CoreBundle\Entity\Manager\LearningMaterialManager');

        $command = new ValidateLearningMaterialPathsCommand(
            $this->iliosFileSystem,
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
        unset($this->iliosFileSystem);
        unset($this->learningMaterialManager);
    }
    
    public function testExecute()
    {
        $goodLm = m::mock('Ilios\CoreBundle\Entity\LearningMaterial');
        $badLm = m::mock('Ilios\CoreBundle\Entity\LearningMaterial')
            ->shouldReceive('getId')->andReturn('42')
            ->shouldReceive('getRelativePath')->andReturn('path/path')
            ->mock();
        $this->learningMaterialManager
            ->shouldReceive('getTotalFileLearningMaterialCount')->andReturn(1)->once()
            ->shouldReceive('findFileLearningMaterials')->andReturn([$goodLm, $badLm])->once()
        ;

        $this->iliosFileSystem
            ->shouldReceive('checkLearningMaterialFilePath')->with($goodLm)->andReturn(true)->once()
            ->shouldReceive('checkLearningMaterialFilePath')->with($badLm)->andReturn(false)->once()
        ;

        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME
        ));
        
        
        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Validated 1 learning materials file path/',
            $output
        );
        $this->assertRegExp(
            '/Unable to find the files for 1 learning material./',
            $output
        );
        $this->assertRegExp(
            '/path/',
            $output
        );
        $this->assertRegExp(
            '/42/',
            $output
        );
    }
}
