<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\Attributes\Group;
use App\Command\MigrateIlios2LearningMaterialsCommand;
use App\Entity\LearningMaterialInterface;
use App\Repository\LearningMaterialRepository;
use App\Service\IliosFileSystem;
use Exception;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class MigrateIlios2LearningMaterialsCommandTest
 * @package App\Tests\Command
 */
#[Group('cli')]
class MigrateIlios2LearningMaterialsCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected m\MockInterface $symfonyFileSystem;
    protected m\MockInterface $iliosFileSystem;
    protected m\MockInterface $learningMaterialRepository;
    private CommandTester $commandTester;

    public function setUp(): void
    {
        parent::setUp();
        $this->symfonyFileSystem = m::mock(Filesystem::class);
        $this->iliosFileSystem = m::mock(IliosFileSystem::class);
        $this->learningMaterialRepository = m::mock(LearningMaterialRepository::class);

        $command = new MigrateIlios2LearningMaterialsCommand(
            $this->symfonyFileSystem,
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
        unset($this->symfonyFileSystem);
        unset($this->iliosFileSystem);
        unset($this->learningMaterialRepository);
        unset($this->commandTester);
    }

    public function testExecute(): void
    {
        $this->symfonyFileSystem->shouldReceive('exists')->with(__DIR__ . '/')->andReturn(true);
        $this->symfonyFileSystem->shouldReceive('exists')->with(__FILE__)->andReturn(true);
        $lm = m::mock(LearningMaterialInterface::class);
        $lm->shouldReceive('getRelativePath')->andReturn(basename(__FILE__))->once();
        $lm->shouldReceive('setRelativePath')->with('newrelativepath')->once();

        $this->learningMaterialRepository->shouldReceive('getTotalFileLearningMaterialCount')->andReturn(1)->once();
        $this->learningMaterialRepository->shouldReceive('findFileLearningMaterials')->andReturn([$lm])->once();
        $this->learningMaterialRepository->shouldReceive('update')->with($lm, false)->once();
        $this->learningMaterialRepository->shouldReceive('flushAndClear')->once();

        $this->iliosFileSystem
            ->shouldReceive('storeLearningMaterialFile')->with(m::on(function ($argument) {
                if ($argument instanceof File && $argument->getRealPath() === __FILE__) {
                    return true;
                }

                return false;
            }))->andReturn('newrelativepath')->once()
        ;
        $this->commandTester->setInputs(['Yes']);

        $this->commandTester->execute([
            'pathToIlios2' => __DIR__ . '/',
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Ready to copy 1 learning materials. Shall we continue?/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Migrated 1 learning materials successfully!/',
            $output
        );
    }

    public function testExecuteWithBadRelativePath(): void
    {
        $this->symfonyFileSystem->shouldReceive('exists')->with('path')->andReturn(true);
        $this->symfonyFileSystem->shouldReceive('exists')->with('path/pathtofile')->andReturn(false);

        $lm = m::mock(LearningMaterialInterface::class);
        $lm->shouldReceive('getRelativePath')->andReturn('/pathtofile')->once();

        $this->learningMaterialRepository->shouldReceive('getTotalFileLearningMaterialCount')->andReturn(1)->once();
        $this->learningMaterialRepository->shouldReceive('findFileLearningMaterials')->andReturn([$lm])->once();
        $this->learningMaterialRepository->shouldReceive('flushAndClear')->once();

        $this->commandTester->setInputs(['Yes']);
        $this->commandTester->execute([
            'pathToIlios2' => 'path',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Migrated 0 learning materials successfully!/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Skipped 1 learning materials because they could not be located or were already migrated./',
            $output
        );
    }

    public function testBadIlios2Path(): void
    {
        $this->symfonyFileSystem->shouldReceive('exists')->with('badpath')->andReturn(false);
        $this->expectException(Exception::class);
        $this->commandTester->execute([
            'pathToIlios2' => 'badpath',
        ]);
    }

    public function testIlios2PathRequired(): void
    {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([]);
    }
}
