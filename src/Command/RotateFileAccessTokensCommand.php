<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\CurriculumInventoryReportRepository;
use App\Repository\LearningMaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Regnerate File Access Tokens
 * Invalidates all existing tokens and generates new ones which is useful if you want to prevent accessing old files
 * or in case of a breach and you want to protect the files.
 */
#[AsCommand(
    name: 'ilios:rotate-tokens',
    description: 'Regenerates access tokens for materials and curriculum inventory reports.',
)]
class RotateFileAccessTokensCommand extends Command
{
    private const int QUERY_LIMIT = 500;

    public function __construct(
        protected readonly EntityManagerInterface $em,
        protected readonly LearningMaterialRepository $learningMaterialRepository,
        protected readonly CurriculumInventoryReportRepository $reportRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'sparse-output',
                null,
                InputOption::VALUE_NONE,
                'Output a plain text sparse output, useful for building a redirect map.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sparseOutput = $input->getOption('sparse-output');
        $this->rotateReportTokens($output, $sparseOutput);
        $this->rotateMaterialTokens($output, $sparseOutput);

        return Command::SUCCESS;
    }

    protected function rotateMaterialTokens(OutputInterface $output, bool $sparseOutput): void
    {
        $ids = $this->learningMaterialRepository->getFileLearningMaterialIds();
        $progress = null;
        if (!$sparseOutput) {
            $progress = new ProgressBar($output, count($ids));
            $progress->setRedrawFrequency(208);
            $output->writeln("<info>Rotating Learning Material Tokens...</info>");
            $progress->start();
        }

        $chunks = array_chunk($ids, self::QUERY_LIMIT);
        $modifiedMaterials = [];
        foreach ($chunks as $ids) {
            $materials = $this->learningMaterialRepository->findBy(['id' => $ids]);
            foreach ($materials as $lm) {
                $originalToken = $lm->getToken();
                $lm->generateToken();
                $newToken = $lm->getToken();
                $this->learningMaterialRepository->update($lm, false);
                $progress?->advance();
                $modifiedMaterials[] = [
                    $lm->getId(),
                    $originalToken,
                    $newToken,
                ];
            }

            $this->em->flush();
            $this->em->clear();
        }

        $progress?->finish();
        if ($sparseOutput) {
            foreach ($modifiedMaterials as $row) {
                $output->writeln("lm/{$row[1]} lm/{$row[2]}");
            }
        } else {
            $table = new Table($output);
            $table
                ->setHeaders(['Material ID', 'Original Token', 'New Token'])
                ->setRows($modifiedMaterials)
            ;
            $table->render();
        }
    }

    protected function rotateReportTokens(OutputInterface $output, bool $sparseOutput): void
    {
        if (!$sparseOutput) {
            $output->writeln("<info>Rotating CI Report Tokens...</info>");
        }
        $modifiedReports = [];
        $reports = $this->reportRepository->findAll();
        foreach ($reports as $report) {
            $originalToken = $report->getToken();
            $report->generateToken();
            $newToken = $report->getToken();
            $this->reportRepository->update($report, false);
            $modifiedReports[] = [
                $report->getId(),
                $originalToken,
                $newToken,
            ];
        }

        $this->em->flush();
        $this->em->clear();

        if ($sparseOutput) {
            foreach ($modifiedReports as $row) {
                $output->writeln("ci-report-dl/{$row[1]} ci-report-dl/{$row[2]}");
            }
        } else {
            $table = new Table($output);
            $table
                ->setHeaders(['Report ID', 'Original Token', 'New Token'])
                ->setRows($modifiedReports)
            ;
            $table->render();
        }
    }
}
