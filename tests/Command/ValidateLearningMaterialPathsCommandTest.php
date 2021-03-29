<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\ValidateLearningMaterialPathsCommand;
use App\Repository\LearningMaterialRepository;
use App\Service\IliosFileSystem;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class ValidateLearningMaterialPathsCommandTest
 * @package App\Tests\Command
 * @group cli
 */
class ValidateLearningMaterialPathsCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private const COMMAND_NAME = 'ilios:validate-learning-materials';

    protected $iliosFileSystem;
    protected $learningMaterialRepository;
    protected $commandTester;

    public function setUp(): void
    {
        parent::setUp();
        $this->iliosFileSystem = m::mock(IliosFileSystem::class);
        $this->learningMaterialRepository = m::mock(LearningMaterialRepository::class);

        $command = new ValidateLearningMaterialPathsCommand(
            $this->iliosFileSystem,
            $this->learningMaterialRepository
        );
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * Remove all mock objects
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->iliosFileSystem);
        unset($this->learningMaterialRepository);
    }

    public function testExecute()
    {
        $goodLm = m::mock('App\Entity\LearningMaterial');
        $badLm = m::mock('App\Entity\LearningMaterial')
            ->shouldReceive('getId')->andReturn('42')
            ->shouldReceive('getRelativePath')->andReturn('path/path')
            ->mock();
        $this->learningMaterialRepository
            ->shouldReceive('getTotalFileLearningMaterialCount')->andReturn(1)->once()
            ->shouldReceive('findFileLearningMaterials')->andReturn([$goodLm, $badLm])->once()
        ;

        $this->iliosFileSystem
            ->shouldReceive('checkLearningMaterialFilePath')->with($goodLm)->andReturn(true)->once()
            ->shouldReceive('checkLearningMaterialFilePath')->with($badLm)->andReturn(false)->once()
        ;

        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Validated 1 learning materials file path/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Unable to find the files for 1 learning material./',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/path/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/42/',
            $output
        );
    }
}
