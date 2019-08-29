<?php

namespace App\Command;

use App\Classes\CurriculumInventoryVerificationReportPreviewBuilder;
use App\Entity\CurriculumInventoryReportInterface;
use App\Entity\Manager\CurriculumInventoryReportManager;
use App\Service\CurriculumInventory\Export\Aggregator;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
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
     * @var CurriculumInventoryReportManager
     */
    protected $reportManager;

    /**
     * GenerateCurriculumInventoryVerificationReportCommand constructor.
     *
     * @param Aggregator $aggregator
     * @param CurriculumInventoryReportManager $reportManager
     */
    public function __construct(Aggregator $aggregator, CurriculumInventoryReportManager $reportManager)
    {
        parent::__construct();
        $this->builder = new CurriculumInventoryVerificationReportPreviewBuilder($aggregator);
        $this->reportManager = $reportManager;
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
}
