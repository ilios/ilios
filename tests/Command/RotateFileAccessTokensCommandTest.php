<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\RotateFileAccessTokensCommand;
use App\Entity\CurriculumInventoryReportInterface;
use App\Repository\CurriculumInventoryReportRepository;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\LearningMaterialInterface;
use App\Repository\LearningMaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

#[Group('cli')]
#[CoversClass(RotateFileAccessTokensCommand::class)]
final class RotateFileAccessTokensCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected m\MockInterface $em;
    protected m\MockInterface $learningMaterialRepository;
    protected m\MockInterface $reportRepository;
    protected CommandTester $commandTester;

    public function setUp(): void
    {
        parent::setUp();
        $this->learningMaterialRepository = m::mock(LearningMaterialRepository::class);
        $this->reportRepository = m::mock(CurriculumInventoryReportRepository::class);
        $this->em = m::mock(EntityManagerInterface::class);

        $command = new RotateFileAccessTokensCommand(
            $this->em,
            $this->learningMaterialRepository,
            $this->reportRepository,
        );
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->addCommands([$command]);
        $commandInApp = $application->find($command->getName());
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * Remove all mock objects
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->em);
        unset($this->learningMaterialRepository);
        unset($this->reportRepository);
    }

    public function testRun(): void
    {
        $this->setupSharedTest();
        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('11        | original report token | new report token', $output);
        $this->assertStringContainsString('22          | original material token | new material token', $output);
    }

    public function testSparseOutput(): void
    {
        $this->setupSharedTest();

        $this->commandTester->execute([
            '--sparse-output' => true,
        ]);

        $output = $this->commandTester->getDisplay();
        $lines = explode("\n", $output);
        $lines = array_filter($lines); //remove empty lines
        $this->assertCount(2, $lines);
        $this->assertEquals('/ci-report-dl/original report token /ci-report-dl/new report token', $lines[0]);
        $this->assertEquals('/lm/original material token /lm/new material token', $lines[1]);
    }

    protected function setupSharedTest(): void
    {
        $report = m::mock(CurriculumInventoryReportInterface::class);
        $report->shouldReceive('getId')->andReturn(11);
        $report->shouldReceive('getToken')->once()->andReturn('original report token');
        $report->shouldReceive('generateToken');
        $report->shouldReceive('getToken')->once()->andReturn('new report token');
        $lm = m::mock(LearningMaterialInterface::class);
        $lm->shouldReceive('getId')->andReturn(22);
        $lm->shouldReceive('getToken')->once()->andReturn('original material token');
        $lm->shouldReceive('generateToken');
        $lm->shouldReceive('getToken')->once()->andReturn('new material token');

        $this->reportRepository->shouldReceive('findAll')->andReturn([$report]);
        $this->learningMaterialRepository->shouldReceive('getFileLearningMaterialIds')->andReturn([22]);
        $this->learningMaterialRepository->shouldReceive('findBy')->with(['id' => [22]])->andReturn([$lm]);

        $this->reportRepository->shouldReceive('update')->with($report, false);
        $this->learningMaterialRepository->shouldReceive('update')->with($lm, false);

        $this->em->shouldReceive('flush')->times(2);
        $this->em->shouldReceive('clear')->times(2);
    }
}
