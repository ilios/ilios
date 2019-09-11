<?php

namespace App\Command;

use App\Service\CurriculumInventory\VerificationPreviewBuilder;
use App\Entity\CurriculumInventoryReportInterface;
use App\Entity\Manager\CurriculumInventoryReportManager;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GenerateCurriculumInventoryVerificationPreviewCommand
 * @package App\Command
 */
class GenerateCurriculumInventoryVerificationPreviewCommand extends Command
{
    /**
     * @var VerificationPreviewBuilder
     */
    protected $builder;

    /**
     * @var CurriculumInventoryReportManager
     */
    protected $reportManager;

    /**
     * GenerateCurriculumInventoryVerificationPreviewCommand constructor.
     *
     * @param CurriculumInventoryReportManager $reportManager
     * @param VerificationPreviewBuilder $builder
     */
    public function __construct(
        CurriculumInventoryReportManager $reportManager,
        VerificationPreviewBuilder $builder
    ) {
        parent::__construct();
        $this->reportManager = $reportManager;
        $this->builder = $builder;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $reportId = $input->getArgument('reportId');
        /* @var CurriculumInventoryReportInterface $report */
        $report = $this->reportManager->findOneBy(['id' => $reportId]);
        if (! $report) {
            $output->writeln("<error>No report with id #${reportId} was found.</error>");
            return;
        }

        $preview = $this->builder->build($report);
        $this->printProgramExpectationsMappedToPcrs($output, $preview['program_expectations_mapped_to_pcrs']);
        $this->printPrimaryInstructionalMethodsByNonClerkshipSequenceBlocks(
            $output,
            $preview['primary_instructional_methods_by_non_clerkship_sequence_blocks']
        );
        $this->printNonClerkshipSequenceBlockInstructionalTime(
            $output,
            $preview['non_clerkship_sequence_block_instructional_time']
        );
        $this->printClerkshipSequenceBlockInstructionalTime(
            $output,
            $preview['clerkship_sequence_block_instructional_time']
        );
        $this->printInstructionalMethodCounts($output, $preview['instructional_method_counts']);
        $this->printNonClerkshipSequenceBlockAssessmentMethods(
            $output,
            $preview['non_clerkship_sequence_block_assessment_methods']
        );
        $this->printClerkshipSequenceBlockAssessmentMethods(
            $output,
            $preview['clerkship_sequence_block_assessment_methods']
        );
        $this->printAllEventsWithAssessmentsTaggedAsFormativeOrSummative(
            $output,
            $preview['all_events_with_assessments_tagged_as_formative_or_summative']
        );
        $this->printAllResourceTypesTable($output, $preview['all_resource_types']);
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('ilios:generate-curriculum-inventory-verification-report-preview')
            ->setDescription(
                'Generates a preview of the verification report tables for a given curriculum inventory report'
            )
            ->addArgument('reportId', InputArgument::REQUIRED, 'The ID of the CI report to preview.');
    }

    /**
     * @param OutputInterface $output
     * @param array $data
     */
    protected function printAllResourceTypesTable(OutputInterface $output, array $data): void
    {
        $this->printTableHeadline($output, 'Table 8: All Resource Types');

        $table = new Table($output);
        $table->setHeaders(['Item Code', 'Resource Types', 'Number of Events']);
        $table->addRows($data);
        $table->render();
    }

    /**
     * @param OutputInterface $output
     * @param array $data
     */
    protected function printInstructionalMethodCounts(OutputInterface $output, array $data): void
    {
        $this->printTableHeadline($output, 'Table 4: Instructional Method Counts');

        $table = new Table($output);
        $table->setHeaders([
            'Item Code',
            'Instructional Method',
            'Number of Events Featuring This as the Primary Method',
            'Number of Non-Primary Occurrences if This Method'
            ]);
        $table->addRows($data);
        $table->addRow(new TableSeparator());
        $primaryMethodTotal = array_sum(array_column($data, 'num_events_primary_method'));
        $nonPrimaryMethodTotal = array_sum(array_column($data, 'num_events_non_primary_method'));

        $summaryRow = [
            '',
            '<options=bold>TOTAL</>',
            "<options=bold>${primaryMethodTotal}</>",
            "<options=bold>${nonPrimaryMethodTotal}</>"
        ];
        $table->addRow($summaryRow);
        $table->render();
    }

    /**
     * @param OutputInterface $output
     * @param array $data
     */
    protected function printAllEventsWithAssessmentsTaggedAsFormativeOrSummative(
        OutputInterface $output,
        array $data
    ): void {
        $this->printTableHeadline($output, 'Table 7: All Events with Assessments Tagged as Formative or Summative');

        $table = new Table($output);
        $table->setHeaders([
            'Item Code',
            'Assessment Method(s)',
            'Number of Summative Assessments',
            'Number of Formative Assessments'
        ]);
        $table->addRows($data);
        $table->addRow(new TableSeparator());
        $summativeAssessmentsTotal = array_sum(array_column($data, 'num_summative_assessments'));
        $formativeAssessmentsTotal = array_sum(array_column($data, 'num_formative_assessments'));

        $summaryRow = [
            '',
            '<options=bold>TOTAL</>',
            "<options=bold>${summativeAssessmentsTotal}</>",
            "<options=bold>${formativeAssessmentsTotal}</>"
        ];
        $table->addRow($summaryRow);
        $table->render();
    }

    /**
     * @param OutputInterface $output
     * @param array $data
     */
    protected function printProgramExpectationsMappedToPcrs(OutputInterface $output, array $data): void
    {
        $this->printTableHeadline($output, 'Table 1: Program Expectations Mapped to PCRS');

        $table = new Table($output);
        $table->setColumnMaxWidth(1, 60);
        $table->setColumnMaxWidth(2, 60);

        $table->setHeaders([
            'Program Expectations ID',
            'Program Expectations',
            'Physician Competency Reference Set (PCRS)',
        ]);

        $rows = [];
        foreach ($data as $expectation) {
            $rows[] = [
                'n/a',
                trim(strip_tags($expectation['title'])),
                implode("\n", $expectation['pcrs'])
            ];
        };

        array_multisort(array_column($rows, 1), SORT_ASC, $rows);


        $lastRow = end($rows);
        foreach ($rows as $row) {
            $table->addRow($row);
            if ($lastRow !== $row) {
                $table->addRow(new TableSeparator());
            }
        }
        $table->render();
    }

    /**
     * @param OutputInterface $output
     * @param array $data
     */
    protected function printPrimaryInstructionalMethodsByNonClerkshipSequenceBlocks(
        OutputInterface $output,
        array $data
    ): void {
        $titles = array_column($data['methods'], 'title');
        $methods = $data['methods'];

        $this->printTableHeadline($output, 'Table 2: Primary Instructional Method by Non-Clerkship Sequence Block');

        $table = new Table($output);
        $table->setColumnMaxWidth(0, 60);
        $table->setColumnMaxWidth(1, 15);
        $table->setHeaders([
            [
                new TableCell('Non-clerkship Sequence Blocks', ['rowspan' => 2]),
                new TableCell('Academic Level', ['rowspan' => 2]),
                new TableCell('Number of Formal Instructional Hours Per Course', ['colspan' => count($titles) + 1])
            ],
            array_merge($titles, ['Total'])
        ]);

        foreach ($data['clerkships'] as $clerkship) {
            $hours = [];
            foreach ($titles as $method) {
                if (array_key_exists($method, $clerkship['instructional_methods'])) {
                    $hours[] = round($clerkship['instructional_methods'][$method] / 60, 2);
                } else {
                    $hours[] = '';
                }
            }
            $total = round($clerkship['total'] / 60, 2);
            $table->addRow(
                array_merge(
                    [$clerkship['title'], $clerkship['level']],
                    $hours,
                    ["<options=bold>$total</>"]
                )
            );
        }

        $table->addRow(new TableSeparator());

        $totals = [];
        $sumTotal = 0;
        foreach ($methods as $method) {
            $totals[] = round($method['total'] / 60, 2);
            $sumTotal += $method['total'];
        }
        $sumTotal = round($sumTotal / 60, 2);



        $table->addRow(
            array_merge(
                ['<options=bold>TOTAL</>', ''],
                array_map(function ($total) {
                    return "<options=bold>${total}</>";
                }, $totals),
                [ "<options=bold>${sumTotal}</>" ]
            )
        );

        $table->render();
    }

    /**
     * @param OutputInterface $output
     * @param array $data
     */
    protected function printNonClerkshipSequenceBlockInstructionalTime(OutputInterface $output, array $data): void
    {
        $this->printTableHeadline($output, 'Table 3-A: Non-Clerkship Sequence Block Instructional Time');
        $table = new Table($output);
        $table->setColumnMaxWidth(0, 60);
        $table->setHeaders([
            'Non-Clerkship Sequence Blocks',
            'Academic Level',
            'Total Weeks',
            'Average Hours of Instruction Per Week'
            ]);
        $table->setRows($data);
        $table->render();
    }

    /**
     * @param OutputInterface $output
     * @param array $data
     */
    protected function printClerkshipSequenceBlockInstructionalTime(OutputInterface $output, array $data): void
    {
        $this->printTableHeadline($output, 'Table 3-B: Clerkship Sequence Block Instructional Time');
        $table = new Table($output);
        $table->setColumnMaxWidth(0, 60);
        $table->setHeaders([
                'Clerkship Sequence Blocks',
                'Academic Level',
                'Total Weeks',
                'Average Hours of Instruction Per Week'
            ]);
        $table->setRows($data);
        $table->render();
    }

    /**
     * @param OutputInterface $output
     * @param array $data
     */
    protected function printNonClerkshipSequenceBlockAssessmentMethods(OutputInterface $output, array $data): void
    {
        $this->printTableHeadline($output, 'Table 5: Non-Clerkship Sequence Block Assessment Methods');
        $table = new Table($output);
        $table->setColumnMaxWidth(0, 50);
        $table->setColumnMaxWidth(1, 15);
        $table->setHeaders([
            [
                new TableCell('Non-clerkship Sequence Blocks', ['rowspan' => 2]),
                new TableCell('Academic Level', ['rowspan' => 2]),
                new TableCell('Formative Asmt.', ['rowspan' => 2]),
                new TableCell('Narrative Asmt.', ['rowspan' => 2]),
                new TableCell('Included in Grade', ['colspan' => count($data['methods']) + 1 ]),
            ],
            array_merge(['Number of Exams'], $data['methods'])
        ]);

        foreach ($data['rows'] as $row) {
            $table->addRow(array_merge([
                $row['title'],
                $row['level'],
                $row['has_formative_assessments'] ? 'Y' : '',
                $row['has_narrative_assessments'] ? 'Y' : '',
                $row['num_exams'] ?: '',
            ], array_map(function ($method) {
                return $method ? 'X' : '';
            }, $row['methods'])));
        }

        $table->render();
    }

    /**
     * @param OutputInterface $output
     * @param array $data
     */
    protected function printClerkshipSequenceBlockAssessmentMethods(OutputInterface $output, array $data): void
    {
        $this->printTableHeadline($output, 'Table 6: Clerkship Sequence Block Assessment Methods');
        $table = new Table($output);
        $table->setColumnMaxWidth(0, 50);
        $table->setColumnMaxWidth(1, 15);
        $table->setHeaders([
            [
                new TableCell('Non-clerkship Sequence Blocks', ['rowspan' => 2]),
                new TableCell('Academic Level', ['rowspan' => 2]),
                new TableCell('Formative Asmt.', ['rowspan' => 2]),
                new TableCell('Narrative Asmt.', ['rowspan' => 2]),
                new TableCell('Included in Grade', ['colspan' => count($data['methods']) ]),
            ],
            $data['methods']
        ]);

        foreach ($data['rows'] as $row) {
            $table->addRow(array_merge([
                $row['title'],
                $row['level'],
                $row['has_formative_assessments'] ? 'Y' : '',
                $row['has_narrative_assessments'] ? 'Y' : '',
            ], array_map(function ($method) {
                return $method ? 'X' : '';
            }, $row['methods'])));
        }

        $table->render();
    }

    /**
     * @param OutputInterface $output
     * @param $title
     */
    protected function printTableHeadline(OutputInterface $output, $title): void
    {
        $output->writeln('');
        $output->writeln("<options=bold,underscore>${title}</>");
        $output->writeln('');
    }
}
