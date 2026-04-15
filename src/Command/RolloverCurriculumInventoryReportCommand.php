<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\CurriculumInventoryReportInterface;
use App\Repository\CurriculumInventoryReportRepository;
use App\Service\CurriculumInventory\ReportRollover;
use Exception;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Rolls over (copies) a given curriculum inventory report.
 */
#[AsCommand(
    name: 'ilios:rollover-ci-report',
    description: 'Rolls over (copies) a given curriculum inventory report.',
    aliases: ['ilios:maintenance:rollover-ci-report'],
)]
class RolloverCurriculumInventoryReportCommand extends Command
{
    public function __construct(
        protected CurriculumInventoryReportRepository $reportRepository,
        protected ReportRollover $service
    ) {
        parent::__construct();
    }

    public function __invoke(
        OutputInterface $output,
        #[Argument(description: 'The id of the report to roll over', name: 'reportId')] int $reportId,
        #[Option(description: 'Name override for the rolled-over report.')] ?string $name = null,
        #[Option(description: 'Description override for the rolled-over report.')] ?string $description = null,
        #[Option(description: 'Academic-year override for the rolled-over report (YYYY).')] ?int $year = null,
    ): int {
        /** @var ?CurriculumInventoryReportInterface $report */
        $report = $this->reportRepository->findOneBy(['id' => $reportId]);
        if (! $report) {
            throw new Exception(
                "No curriculum inventory report with id #{$reportId} was found."
            );
        }

        $newReport = $this->service->rollover($report, $report->getProgram(), $name, $description, $year);

        //output message with the new courseId on success
        $output->writeln("The given report has been rolled over. The new report id is {$newReport->getId()}.");

        return Command::SUCCESS;
    }
}
