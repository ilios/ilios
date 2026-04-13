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
                'Phases (Start - End)',
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

    public function testExecuteVerificationPreviewTable3a(): void
    {
        $report = m::mock(CurriculumInventoryReportInterface::class);
        $this->reportRepository->shouldReceive('findOneBy')->with(['id' => '1'])->andReturn($report);

        $data = $this->getEmptyData();
        $data['non_clerkship_sequence_block_instructional_time'] = [
            [
                'title' => 'Block 1',
                'starting_level' => 1,
                'ending_level' => 2,
                'weeks' => 15,
                'avg' => 5.55,
            ],
            [
                'title' => 'Block 2',
                'starting_level' => 2,
                'ending_level' => 3,
                'weeks' => 2,
                'avg' => 7,
            ],
        ];

        $this->builder->shouldReceive('build')->with($report)->andReturn($data);

        $this->commandTester->execute(['reportId' => '1']);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Table 3-A: Non-Clerkship Sequence Block Instructional Time/',
            $output
        );
        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                'Non-Clerkship Sequence Blocks',
                'Phases (Start - End)',
                'Total Weeks',
                'Average Hours of Instruction Per Week',
            ]),
            $output
        );

        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                'Block 1',
                '1 - 2',
                '15',
                '5.55',
            ]),
            $output
        );
        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                'Block 2',
                '2 - 3',
                '2',
                '7',
            ]),
            $output
        );
    }

    public function testExecuteVerificationPreviewTable3b(): void
    {
        $report = m::mock(CurriculumInventoryReportInterface::class);
        $this->reportRepository->shouldReceive('findOneBy')->with(['id' => '1'])->andReturn($report);

        $data = $this->getEmptyData();
        $data['clerkship_sequence_block_instructional_time'] = [
            [
                'title' => 'Block 1',
                'starting_level' => 1,
                'ending_level' => 2,
                'weeks' => 15,
                'avg' => 5.55,
            ],
            [
                'title' => 'Block 2',
                'starting_level' => 2,
                'ending_level' => 3,
                'weeks' => 2,
                'avg' => 7,
            ],
        ];

        $this->builder->shouldReceive('build')->with($report)->andReturn($data);

        $this->commandTester->execute(['reportId' => '1']);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Table 3-B: Clerkship Sequence Block Instructional Time/',
            $output
        );
        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                'Clerkship Sequence Blocks',
                'Phases (Start - End)',
                'Total Weeks',
                'Average Hours of Instruction Per Week',
            ]),
            $output
        );

        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                'Block 1',
                '1 - 2',
                '15',
                '5.55',
            ]),
            $output
        );
        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                'Block 2',
                '2 - 3',
                '2',
                '7',
            ]),
            $output
        );
    }

    public function testExecuteVerificationPreviewTable4(): void
    {
        $report = m::mock(CurriculumInventoryReportInterface::class);
        $this->reportRepository->shouldReceive('findOneBy')->with(['id' => '1'])->andReturn($report);

        $data = $this->getEmptyData();
        $data['instructional_method_counts'] = [
            [
                'id' => 'IM001',
                'title' => 'Case-Based Instruction/Learning',
                'num_events_primary_method' => 12,
                'num_events_non_primary_method' => 0,
            ],
            [
                'id' => 'IM002',
                'title' => 'Clinical Expertise - Ambulatory',
                'num_events_primary_method' => 0,
                'num_events_non_primary_method' => 10,
            ],
            [
                'id' => 'IM003',
                'title' => 'Clinical Expertise - Inpatient',
                'num_events_primary_method' => 4,
                'num_events_non_primary_method' => 9,
            ],
        ];

        $this->builder->shouldReceive('build')->with($report)->andReturn($data);

        $this->commandTester->execute(['reportId' => '1']);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Table 4: Instructional Method Counts/',
            $output
        );
        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                'Item Code',
                'Instructional Method',
                'Number of Events Featuring This as the Primary Method',
                'Number of Non-Primary Occurrences of This Method',
            ]),
            $output
        );

        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                'IM001',
                'Case-Based Instruction/Learning',
                '12',
                '0',
            ]),
            $output
        );

        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                'IM002',
                'Clinical Expertise - Ambulatory',
                '0',
                '10',
            ]),
            $output
        );

        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                'IM003',
                'Clinical Expertise - Inpatient',
                '4',
                '9',
            ]),
            $output
        );

        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                '',
                'TOTAL',
                '16',
                '19',
            ]),
            $output
        );
    }
    public function testExecuteVerificationPreviewTable5(): void
    {
        $report = m::mock(CurriculumInventoryReportInterface::class);
        $this->reportRepository->shouldReceive('findOneBy')->with(['id' => '1'])->andReturn($report);

        $data = $this->getEmptyData();
        $data['non_clerkship_sequence_block_assessment_methods'] = [
            'methods' => [
                'Faculty / resident rating',
                'Internal exams',
                'Lab or practical exams',
                'NBME subject exams',
                'OSCE / SP exam',
                'Other',
                'Paper or oral pres.',
            ],
            'rows' => [
                [
                    'title' => 'Block 1',
                    'starting_level' => 1,
                    'ending_level' => 3,
                    'methods' => [
                        'Faculty /resident rating' => true,
                        'Internal exams' => false,
                        'Lab or practical exams' => true,
                        'NBME subject exams' => false,
                        'OSCE / SP exam' => true,
                        'Other' => false,
                        'Paper or oral pres.' => true,
                    ],
                    'num_exams' => 5,
                    'has_formative_assessments' => true,
                    'has_narrative_assessments' => false,
                ],
                [
                    'title' => 'Block 2',
                    'starting_level' => 2,
                    'ending_level' => 4,
                    'methods' => [
                        'Faculty /resident rating' => false,
                        'Internal exams' => true,
                        'Lab or practical exams' => false,
                        'NBME subject exams' => true,
                        'OSCE / SP exam' => false,
                        'Other' => true,
                        'Paper or oral pres.' => false,
                    ],
                    'num_exams' => 6,
                    'has_formative_assessments' => false,
                    'has_narrative_assessments' => true,
                ],
            ],
        ];

        $this->builder->shouldReceive('build')->with($report)->andReturn($data);

        $this->commandTester->execute(['reportId' => '1']);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Table 5: Non-Clerkship Sequence Block Assessment Methods/',
            $output
        );
        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                'Non-Clerkship Sequence Blocks',
                'Phases (Start - End)',
                'Included in Grade',
                'Assessments',
            ]),
            $output
        );
        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                '',
                '',
                'Number of Exams',
                'Faculty / resident rating',
                'Internal exams',
                'Lab or practical exams',
                'NBME subject exams',
                'OSCE / SP exam',
                'Other',
                'Paper or oral pres.',
                'Formative',
                'Narrative',
            ]),
            $output
        );
        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                'Block 1',
                '1 - 3',
                '5',
                'X',
                '',
                'X',
                '',
                'X',
                '',
                'X',
                'Y',
                '',
            ]),
            $output
        );
        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                'Block 2',
                '2 - 4',
                '6',
                '',
                'X',
                '',
                'X',
                '',
                'X',
                '',
                '',
                'Y',
            ]),
            $output
        );
    }
    public function testExecuteVerificationPreviewTable6(): void
    {
        $report = m::mock(CurriculumInventoryReportInterface::class);
        $this->reportRepository->shouldReceive('findOneBy')->with(['id' => '1'])->andReturn($report);

        $data = $this->getEmptyData();
        $data['clerkship_sequence_block_assessment_methods'] = [
            'methods' => [
                'Faculty / resident rating',
                'Internal written exams',
                'NBME subject exams',
                'OSCE / SP exam',
                'Oral Exam or Pres.',
                'Other',
            ],
            'rows' => [
                [
                    'title' => 'Block 1',
                    'starting_level' => 1,
                    'ending_level' => 3,
                    'methods' => [
                        'Faculty /resident rating' => true,
                        'Internal written exams' => false,
                        'NBME subject exams' => true,
                        'OSCE / SP exam' => false,
                        'Oral Exam or Pres.' => true,
                        'Other' => false,
                    ],
                    'num_exams' => 5,
                    'has_formative_assessments' => true,
                    'has_narrative_assessments' => true,
                ],
                [
                    'title' => 'Block 2',
                    'starting_level' => 2,
                    'ending_level' => 4,
                    'methods' => [
                        'Faculty /resident rating' => false,
                        'Internal written exams' => true,
                        'NBME subject exams' => false,
                        'OSCE / SP exam' => true,
                        'Oral Exam or Pres.' => false,
                        'Other' => true,
                    ],
                    'num_exams' => 5,
                    'has_formative_assessments' => false,
                    'has_narrative_assessments' => false,
                ],
            ],
        ];

        $this->builder->shouldReceive('build')->with($report)->andReturn($data);

        $this->commandTester->execute(['reportId' => '1']);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Table 6: Clerkship Sequence Block Assessment Methods/',
            $output
        );
        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                'Clerkship Sequence Blocks',
                'Phases (Start - End)',
                'Included in Grade',
                'Assessments',
            ]),
            $output
        );
        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                '',
                '',
                'Faculty / resident rating',
                'Internal written exams',
                'NBME subject exams',
                'OSCE / SP exam',
                'Oral Exam or Pres.',
                'Other',
                'Formative',
                'Narrative',
            ]),
            $output
        );
        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                'Block 1',
                '1 - 3',
                'X',
                '',
                'X',
                '',
                'X',
                '',
                'Y',
                'Y',
            ]),
            $output
        );
        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                'Block 2',
                '2 - 4',
                '',
                'X',
                '',
                'X',
                '',
                'X',
                '',
                '',
            ]),
            $output
        );
    }

    public function testExecuteVerificationPreviewTable7(): void
    {
        $report = m::mock(CurriculumInventoryReportInterface::class);
        $this->reportRepository->shouldReceive('findOneBy')->with(['id' => '1'])->andReturn($report);

        $data = $this->getEmptyData();
        $data['all_events_with_assessments_tagged_as_formative_or_summative'] = [
            [
                'id' => 'AM001',
                'title' => 'Clinical Documentation Review',
                'num_summative_assessments' => 12,
                'num_formative_assessments' => 0,
            ],
            [
                'id' => 'AM002',
                'title' => 'Clinical Performance Rating/Checklist',
                'num_summative_assessments' => 8,
                'num_formative_assessments' => 10,
            ],
        ];

        $this->builder->shouldReceive('build')->with($report)->andReturn($data);

        $this->commandTester->execute(['reportId' => '1']);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Table 7: All Events with Assessments Tagged as Formative or Summative/',
            $output
        );
        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                'Item Code',
                'Assessment Methods',
                'Number of Summative Assessments',
                'Number of Formative Assessments',
            ]),
            $output
        );
        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                'AM001',
                'Clinical Documentation Review',
                '12',
                '0',
            ]),
            $output
        );
        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                'AM002',
                'Clinical Performance Rating/Checklist',
                '8',
                '10',
            ]),
            $output
        );
        $this->assertMatchesRegularExpression(
            $this->buildTableRowRegex([
                '',
                'TOTAL',
                '20',
                '10',
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
