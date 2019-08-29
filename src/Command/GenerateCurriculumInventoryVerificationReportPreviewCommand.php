<?php

namespace App\Command;

use App\Classes\CurriculumInventoryVerificationReportPreviewBuilder;
use App\Entity\CurriculumInventoryReportInterface;
use App\Entity\Manager\AamcMethodManager;
use App\Entity\Manager\CurriculumInventoryReportManager;
use App\Service\CurriculumInventory\Export\Aggregator;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class GenerateCurriculumInventoryVerificationReportCommand
 * @package App\Command
 */
class GenerateCurriculumInventoryVerificationReportPreviewCommand extends Command
{
    /**
     * @var CurriculumInventoryVerificationReportPreviewBuilder
     */
    protected $builder;

    /**
     * @var AamcMethodManager
     */
    protected $methodManager;

    /**
     * @var CurriculumInventoryReportManager
     */
    protected $reportManager;

    /**
     * GenerateCurriculumInventoryVerificationReportCommand constructor.
     *
     * @param Aggregator $aggregator
     * @param AamcMethodManager $methodManager
     * @param CurriculumInventoryReportManager $reportManager
     */
    public function __construct(
        Aggregator $aggregator,
        AamcMethodManager $methodManager,
        CurriculumInventoryReportManager $reportManager)
    {
        parent::__construct();
        $this->methodManager = $methodManager;
        $this->reportManager = $reportManager;
        $this->builder = new CurriculumInventoryVerificationReportPreviewBuilder($aggregator, $this->methodManager);
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
            $output->writeln("<error>No report with id #{$reportId} was found.</error>");
            return;
        }

        $preview = $this->builder->build($report);
        $this->printInstructionalMethodCounts($output, $preview['instructional-method-counts']);
        $this->printAllResourceTypesTable($output, $preview['all-resource-types']);
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

    protected function printAllResourceTypesTable(OutputInterface $output, array $data)
    {
        $table = new Table($output);
        $table->setHeaders(['Item Code', 'Resource Types', 'Number of Events']);
        $table->setHeaderTitle('Table 8: All Resource Types');
        $table->addRows($data);
        $table->render();
    }

    protected function printInstructionalMethodCounts(OutputInterface $output, array $data)
    {
        $table = new Table($output);
        $table->setHeaders([
            'Item Code',
            'Instructional Method',
            'Number of Events Featuring This as the Primary Method',
            'Number of Non-Primary Occurrences if This Method'
            ]);
        $table->setHeaderTitle('Table 4: Instructional Method Counts');
        $table->addRows($data);
        $table->addRow(new TableSeparator());
        $primaryMethodTotal = array_sum(array_column($data,'num-events-primary-method'));
        $nonPrimaryMethodTotal = array_sum(array_column($data,'num-events-non-primary-method'));

        $summaryRow = [
            '',
            '<options=bold>TOTAL</>',
            "<options=bold>${primaryMethodTotal}</>",
            "<options=bold>${nonPrimaryMethodTotal}</>"
        ];
        $table->addRow($summaryRow);
        $table->render();
    }
}
