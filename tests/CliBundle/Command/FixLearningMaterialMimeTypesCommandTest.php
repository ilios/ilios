<?php
namespace Tests\CliBundle\Command;

use Ilios\CliBundle\Command\FixLearningMaterialMimeTypesCommand;
use Ilios\CoreBundle\Entity\LearningMaterialInterface;
use Ilios\CoreBundle\Entity\Manager\LearningMaterialManager;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Ilios\CoreBundle\Service\IliosFileSystem;
use Symfony\Component\HttpFoundation\File\File;

class FixLearningMaterialMimeTypesCommandTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:maintenance:fix-mime-types';
    
    protected $iliosFileSystem;
    protected $learningMaterialManager;
    protected $commandTester;

    public function setUp()
    {
        $this->iliosFileSystem = m::mock(IliosFileSystem::class);
        $this->learningMaterialManager = m::mock(LearningMaterialManager::class);

        $command = new FixLearningMaterialMimeTypesCommand(
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
        unset($this->commandTester);
    }
    
    public function testFixCitationType()
    {
        $this->learningMaterialManager->shouldReceive('getTotalLearningMaterialCount')
            ->once()->andReturn(1);
        $mockLm = m::mock(LearningMaterialInterface::class);
        $mockLm->shouldReceive('getRelativePath')->once()->andReturn(null);
        $mockLm->shouldReceive('getCitation')->once()->andReturn('MST3k The Return S1 E6');
        $mockLm->shouldReceive('getMimetype')->once()->andReturn('link');
        $mockLm->shouldReceive('setMimetype')->with('citation')->once();
        $this->learningMaterialManager->shouldReceive('findBy')->with([], ['id' => 'desc'], 50, 0)
            ->once()->andReturn([$mockLm]);
        $this->learningMaterialManager->shouldReceive('update')->with($mockLm, false);
        $this->learningMaterialManager->shouldReceive('flushAndClear')->once();

        $this->commandTester->setInputs(['Yes']);
        $this->commandTester->execute([
            'command' => self::COMMAND_NAME
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Ready to fix 1 learning materials. Shall we continue?/',
            $output
        );
        $this->assertRegExp(
            '/Updated mimetypes for 1 learning materials successfully!/',
            $output
        );
    }

    public function testFixBlankCitationType()
    {
        $this->learningMaterialManager->shouldReceive('getTotalLearningMaterialCount')
            ->once()->andReturn(1);
        $mockLm = m::mock(LearningMaterialInterface::class);
        $mockLm->shouldReceive('getRelativePath')->once()->andReturn(null);
        $mockLm->shouldReceive('getCitation')->once()->andReturn('');
        $mockLm->shouldReceive('getMimetype')->once()->andReturn('link');
        $mockLm->shouldReceive('setMimetype')->with('citation')->once();
        $this->learningMaterialManager->shouldReceive('findBy')->with([], ['id' => 'desc'], 50, 0)
            ->once()->andReturn([$mockLm]);
        $this->learningMaterialManager->shouldReceive('update')->with($mockLm, false);
        $this->learningMaterialManager->shouldReceive('flushAndClear')->once();

        $this->commandTester->setInputs(['Yes']);
        $this->commandTester->execute([
            'command' => self::COMMAND_NAME
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Ready to fix 1 learning materials. Shall we continue?/',
            $output
        );
        $this->assertRegExp(
            '/Updated mimetypes for 1 learning materials successfully!/',
            $output
        );
    }

    public function testFixLinkType()
    {
        $this->learningMaterialManager->shouldReceive('getTotalLearningMaterialCount')
            ->once()->andReturn(1);
        $mockLm = m::mock(LearningMaterialInterface::class);
        $mockLm->shouldReceive('getRelativePath')->once()->andReturn(null);
        $mockLm->shouldReceive('getCitation')->once()->andReturn(null);
        $mockLm->shouldReceive('getLink')->once()->andReturn('https://example.com');
        $mockLm->shouldReceive('getMimetype')->once()->andReturn('citation');
        $mockLm->shouldReceive('setMimetype')->with('link')->once();
        $this->learningMaterialManager->shouldReceive('findBy')->with([], ['id' => 'desc'], 50, 0)
            ->once()->andReturn([$mockLm]);
        $this->learningMaterialManager->shouldReceive('update')->with($mockLm, false);
        $this->learningMaterialManager->shouldReceive('flushAndClear')->once();

        $this->commandTester->setInputs(['Yes']);
        $this->commandTester->execute([
            'command' => self::COMMAND_NAME
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Ready to fix 1 learning materials. Shall we continue?/',
            $output
        );
        $this->assertRegExp(
            '/Updated mimetypes for 1 learning materials successfully!/',
            $output
        );
    }

    public function testFixBlankLinkType()
    {
        $this->learningMaterialManager->shouldReceive('getTotalLearningMaterialCount')
            ->once()->andReturn(1);
        $mockLm = m::mock(LearningMaterialInterface::class);
        $mockLm->shouldReceive('getRelativePath')->once()->andReturn(null);
        $mockLm->shouldReceive('getCitation')->once()->andReturn(null);
        $mockLm->shouldReceive('getLink')->once()->andReturn('');
        $mockLm->shouldReceive('getMimetype')->once()->andReturn('citation');
        $mockLm->shouldReceive('setMimetype')->with('link')->once();
        $this->learningMaterialManager->shouldReceive('findBy')->with([], ['id' => 'desc'], 50, 0)
            ->once()->andReturn([$mockLm]);
        $this->learningMaterialManager->shouldReceive('update')->with($mockLm, false);
        $this->learningMaterialManager->shouldReceive('flushAndClear')->once();

        $this->commandTester->setInputs(['Yes']);
        $this->commandTester->execute([
            'command' => self::COMMAND_NAME
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Ready to fix 1 learning materials. Shall we continue?/',
            $output
        );
        $this->assertRegExp(
            '/Updated mimetypes for 1 learning materials successfully!/',
            $output
        );
    }

    public function testFixFileType()
    {
        $this->learningMaterialManager->shouldReceive('getTotalLearningMaterialCount')
            ->once()->andReturn(1);
        $mockLm = m::mock(LearningMaterialInterface::class);
        $mockLm->shouldReceive('getRelativePath')->once()->andReturn('/tmp/somewhere');
        $mockLm->shouldReceive('getMimetype')->once()->andReturn('citation');
        $mockLm->shouldReceive('setMimetype')->with('pdf-type-file')->once();

        $mockFile = m::mock(File::class)->shouldReceive('getMimeType')->once()->andReturn('pdf-type-file')->mock();
        $this->iliosFileSystem->shouldReceive('getFile')->once()->with('/tmp/somewhere')->andReturn($mockFile);

        $this->learningMaterialManager->shouldReceive('findBy')->with([], ['id' => 'desc'], 50, 0)
            ->once()->andReturn([$mockLm]);
        $this->learningMaterialManager->shouldReceive('update')->with($mockLm, false);
        $this->learningMaterialManager->shouldReceive('flushAndClear')->once();

        $this->commandTester->setInputs(['Yes']);
        $this->commandTester->execute([
            'command' => self::COMMAND_NAME
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Ready to fix 1 learning materials. Shall we continue?/',
            $output
        );
        $this->assertRegExp(
            '/Updated mimetypes for 1 learning materials successfully!/',
            $output
        );
    }

    public function testFixFileWithUndetectableTypeType()
    {
        $this->learningMaterialManager->shouldReceive('getTotalLearningMaterialCount')
            ->once()->andReturn(1);
        $mockLm = m::mock(LearningMaterialInterface::class);
        $mockLm->shouldReceive('getRelativePath')->once()->andReturn('/tmp/somewhere');
        $mockLm->shouldReceive('getMimetype')->once()->andReturn('citation');
        $mockLm->shouldReceive('setMimetype')->with('application/pdf')->once();
        $mockLm->shouldReceive('getFilename')->once()->andReturn('test.pdf');


        $mockFile = m::mock(File::class)->shouldReceive('getMimeType')
            ->once()->andThrow(\ErrorException::class)->mock();
        $this->iliosFileSystem->shouldReceive('getFile')->once()->with('/tmp/somewhere')->andReturn($mockFile);

        $this->learningMaterialManager->shouldReceive('findBy')->with([], ['id' => 'desc'], 50, 0)
            ->once()->andReturn([$mockLm]);
        $this->learningMaterialManager->shouldReceive('update')->with($mockLm, false);
        $this->learningMaterialManager->shouldReceive('flushAndClear')->once();

        $this->commandTester->setInputs(['Yes']);
        $this->commandTester->execute([
            'command' => self::COMMAND_NAME
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Ready to fix 1 learning materials. Shall we continue?/',
            $output
        );
        $this->assertRegExp(
            '/Updated mimetypes for 1 learning materials successfully!/',
            $output
        );
    }
}
