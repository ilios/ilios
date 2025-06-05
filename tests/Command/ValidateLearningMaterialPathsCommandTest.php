<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\Attributes\Group;
use App\Command\ValidateLearningMaterialPathsCommand;
use App\Entity\LearningMaterialInterface;
use App\Repository\LearningMaterialRepository;
use App\Service\IliosFileSystem;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class ValidateLearningMaterialPathsCommandTest
 * @package App\Tests\Command
 */
#[Group('cli')]
final class ValidateLearningMaterialPathsCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected m\MockInterface $iliosFileSystem;
    protected m\MockInterface $learningMaterialRepository;
    protected CommandTester $commandTester;

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
        $commandInApp = $application->find($command->getName());
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
        unset($this->commandTester);
    }

    public function testExecute(): void
    {
        $goodLm = m::mock(LearningMaterialInterface::class);
        $badLm = m::mock(LearningMaterialInterface::class);
        $badLm->shouldReceive('getId')->andReturn('42');
        $badLm->shouldReceive('getRelativePath')->andReturn('path/path');

        $this->learningMaterialRepository
            ->shouldReceive('getTotalFileLearningMaterialCount')->andReturn(1)->once();
        $this->learningMaterialRepository
            ->shouldReceive('findFileLearningMaterials')->andReturn([$goodLm, $badLm])->once();

        $this->iliosFileSystem
            ->shouldReceive('checkLearningMaterialFilePath')->with($goodLm)->andReturn(true)->once();
        $this->iliosFileSystem
            ->shouldReceive('checkLearningMaterialFilePath')->with($badLm)->andReturn(false)->once();

        $this->commandTester->execute([]);

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
