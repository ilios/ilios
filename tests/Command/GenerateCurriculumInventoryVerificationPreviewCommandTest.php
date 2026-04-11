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

        $data = $this->getEmptyData();

        $this->builder->shouldReceive('build')->with($report)->andReturn($data);

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

    public function testExecuteVerificationPreviewTable1(): void
    {
        $report = m::mock(CurriculumInventoryReportInterface::class);
        $this->reportRepository->shouldReceive('findOneBy')->with(['id' => '1'])->andReturn($report);

        $data = $this->getEmptyData();
        $data['program_expectations_mapped_to_pcrs'] = [
            [
                'title' => 'foo',
                'pcrs' => [
                    'bar',
                    'baz',
                ],
            ],
            [
                'title' => 'bat',
                'pcrs' => [],
            ],
        ];

        $this->builder->shouldReceive('build')->with($report)->andReturn($data);

        $this->commandTester->execute(['reportId' => '1']);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression('/Table 1: Program Expectations Mapped to PCRS/', $output);
        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                'Program Expectations ID',
                'Program Expectations',
                'Physician Competency Reference Set (PCRS)',
            ]),
            $output
        );
        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                'n/a',
                'bat',
                '',
            ]),
            $output,
        );
        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                'n/a',
                'foo',
                'bar',
            ]),
            $output,
        );
        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                '',
                '',
                'baz',
            ]),
            $output,
        );
    }

    public function testExecuteVerificationPreviewTable2(): void
    {
        $report = m::mock(CurriculumInventoryReportInterface::class);
        $this->reportRepository->shouldReceive('findOneBy')->with(['id' => '1'])->andReturn($report);

        $data = $this->getEmptyData();
        $data['primary_instructional_methods_by_non_clerkship_sequence_blocks'] = [
            'methods' => [
                [
                    'title' => 'Lab',
                    'total' => 60,
                ],
                [
                    'title' => 'Lecture',
                    'total' => 180,
                ],
                [
                    'title' => 'Other',
                    'total' => 630,
                ],
            ],
            'rows' => [
                [
                    'title' => 'Block 1',
                    'starting_level' => 1,
                    'ending_level' => 3,
                    'instructional_methods' => [
                        'Lab' => 30,
                        'Lecture' => 60,
                        'Other' => 600,
                    ],
                    'total' => 690,
                ],
                [
                    'title' => 'Block 2',
                    'starting_level' => 2,
                    'ending_level' => 4,
                    'instructional_methods' => [
                        'Lab' => 30,
                        'Lecture' => 120,
                        'Other' => 30,
                    ],
                    'total' => 180,
                ],
            ],
        ];

        $this->builder->shouldReceive('build')->with($report)->andReturn($data);

        $this->commandTester->execute(['reportId' => '1']);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Table 2: Primary Instructional Method by Non-Clerkship Sequence Block/',
            $output
        );
        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                'Non-clerkship Sequence Blocks',
                'Academic Level',
                'Number of Formal Instructional Hours Per Course',
            ]),
            $output
        );
        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                '',
                'Lab',
                'Lecture',
                'Other',
                'Total',
            ]),
            $output
        );
        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                'Block 1',
                '1 - 3',
                '0.5',
                '1',
                '10',
                '11.5',
            ]),
            $output
        );
        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                'Block 2',
                '2 - 4',
                '0.5',
                '2',
                '0.5',
                '3',
            ]),
            $output
        );
        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                'TOTAL',
                '',
                '1',
                '3',
                '10.5',
                '14.5',
            ]),
            $output
        );
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

    protected function getEmptyData(): array
    {
        return [
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
    }

    protected function buildTableRowRegex(array $parts): string
    {
        $rhett = '/';
        foreach ($parts as $part) {
            if ('' === $part) {
                $rhett .= '\|\s+';
            } else {
                $rhett .= '\|\s+' . preg_quote($part, '/') . '\s+';
            }
        }
        $rhett .= '\|/';
        return $rhett;
    }
}
