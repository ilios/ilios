<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\GenerateCurriculumInventoryVerificationPreviewCommand;
use App\Entity\CurriculumInventoryReportInterface;
use App\Repository\CurriculumInventoryReportRepository;
use App\Service\CurriculumInventory\VerificationPreviewBuilder;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Attributes\Group;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

#[Group('cli')]
final class GenerateCurriculumInventoryVerificationPreviewCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected m\MockInterface $reportRepository;
    protected m\MockInterface $builder;
    protected CommandTester $commandTester;

    public function setUp(): void
    {
        parent::setUp();
        $this->reportRepository = m::mock(CurriculumInventoryReportRepository::class);
        $this->builder = m::mock(VerificationPreviewBuilder::class);

        $command = new GenerateCurriculumInventoryVerificationPreviewCommand(
            $this->reportRepository,
            $this->builder
        );
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->addCommands([$command]);
        $commandInApp = $application->find($command->getName());
        $this->commandTester = new CommandTester($commandInApp);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->reportRepository);
        unset($this->builder);
        unset($this->commandTester);
    }

    public function testExecute(): void
    {
        $report = m::mock(CurriculumInventoryReportInterface::class);
        $this->reportRepository->shouldReceive('findOneBy')->with(['id' => '1'])->andReturn($report);

        $preview = [
            'program_expectations_mapped_to_pcrs' => [],
            'primary_instructional_methods_by_non_clerkship_sequence_blocks' => [
                'methods' => [],
                'rows' => [],
            ],
            'non_clerkship_sequence_block_instructional_time' => [],
            'clerkship_sequence_block_instructional_time' => [],
            'instructional_method_counts' => [],
            'non_clerkship_sequence_block_assessment_methods' => [
                'methods' => ['Internal exams'],
                'rows' => [],
            ],
            'clerkship_sequence_block_assessment_methods' => [
                'methods' => ['NBME subject exams'],
                'rows' => [],
            ],
            'all_events_with_assessments_tagged_as_formative_or_summative' => [],
            'all_resource_types' => [],
        ];

        $this->builder->shouldReceive('build')->with($report)->andReturn($preview);

        $this->commandTester->execute(['reportId' => '1']);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression('/Table 1: Program Expectations Mapped to PCRS/', $output);
        $this->assertMatchesRegularExpression(
            '/Table 2: Primary Instructional Method by Non-Clerkship Sequence Block/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Table 3-A: Non-Clerkship Sequence Block Instructional Time/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Table 3-B: Clerkship Sequence Block Instructional Time/',
            $output
        );
        $this->assertMatchesRegularExpression('/Table 4: Instructional Method Counts/', $output);
        $this->assertMatchesRegularExpression(
            '/Table 5: Non-Clerkship Sequence Block Assessment Methods/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Table 6: Clerkship Sequence Block Assessment Methods/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Table 7: All Events with Assessments Tagged as Formative or Summative/',
            $output
        );
        $this->assertMatchesRegularExpression('/Table 8: All Resource Types/', $output);
        $this->assertSame(Command::SUCCESS, $this->commandTester->getStatusCode());
    }

    public function testReportNotFound(): void
    {
        $this->reportRepository->shouldReceive('findOneBy')->with(['id' => '1'])->andReturn(null);

        $this->commandTester->execute(['reportId' => '1']);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression('/No report with id #1 was found\./', $output);
        $this->assertSame(Command::FAILURE, $this->commandTester->getStatusCode());
    }

    public function testReportIdRequired(): void
    {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([]);
    }
}
