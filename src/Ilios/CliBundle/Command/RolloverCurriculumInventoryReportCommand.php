<?php

namespace Ilios\CliBundle\Command;

use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;
use Ilios\CoreBundle\Entity\Manager\CurriculumInventoryReportManager;
use Ilios\CoreBundle\Service\CurriculumInventory\ReportRollover;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Rolls over (copies) a given curriculum inventory report.
 *
 * Class RolloverCurriculumInventoryReportCommand
 */
class RolloverCurriculumInventoryReportCommand extends Command
{
    /**
     * @var CurriculumInventoryReportManager
     */
    protected $reportManager;
    /**
     * @var ReportRollover
     */
    protected $service;

    /**
     * @param CurriculumInventoryReportManager $reportManager
     * @param ReportRollover $service
     */
    public function __construct(CurriculumInventoryReportManager $reportManager, ReportRollover $service)
    {
        $this->reportManager =$reportManager;
        $this->service = $service;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('ilios:maintenance:rollover-ci-report')
            ->setDescription('Rolls over (copies) a given curriculum inventory report.')
            //required arguments
            ->addArgument(
                'reportId',
                InputArgument::REQUIRED,
                'The id of the report to roll over.'
            )
            ->addOption(
                'name',
                null,
                InputOption::VALUE_REQUIRED,
                'Name override for the rolled-over report.'
            )
            //optional flags
            ->addOption(
                'description',
                null,
                InputOption::VALUE_REQUIRED,
                'Description override for the rolled-over report.'
            )
            ->addOption(
                'year',
                null,
                InputOption::VALUE_REQUIRED,
                'Academic-year override for the rolled-over report (YYYY).'
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $reportId = $input->getArgument('reportId');
        $name = $input->getOption('name');
        $description = $input->getOption('description');
        $year = $input->getOption('year');

        /* @var CurriculumInventoryReportInterface $report */
        $report = $this->reportManager->findOneBy(['id' => $reportId]);
        if (! $report) {
            throw new \Exception(
                "No curriculum inventory report with id #{$reportId} was found."
            );
        }

        $newReport = $this->service->rollover($report, $name, $description, $year);

        //output message with the new courseId on success
        $output->writeln("The given report has been rolled over. The new report id is {$newReport->getId()}.");
    }
}
