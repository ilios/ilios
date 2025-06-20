<?php

declare(strict_types=1);

namespace App\Command\Index;

use App\Repository\LearningMaterialRepository;
use App\Repository\SessionRepository;
use App\Service\Index\Curriculum;
use App\Service\Index\LearningMaterials;
use DateTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ilios:index:detect-missing',
    description: 'Find items that are missing in the index'
)]
class DetectMissingCommand extends Command
{
    public function __construct(
        protected readonly LearningMaterialRepository $learningMaterialRepository,
        protected readonly SessionRepository $sessionRepository,
        protected readonly LearningMaterials $materialIndex,
        protected readonly Curriculum $curriculumIndex,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->materialIndex->isEnabled()) {
            $output->writeln("<comment>Indexing is not currently configured.</comment>");
            return Command::FAILURE;
        }
        $io = new SymfonyStyle($input, $output);
        $missingMaterials = $this->checkMaterials();
        $missingSessions = $this->checkSessions();

        if (!$missingMaterials && !$missingSessions) {
            $io->success("All Items Indexed.");
            return Command::SUCCESS;
        }

        return $this->displayResults($io, $output, $missingMaterials, $missingSessions);
    }

    protected function checkMaterials(): array
    {
        $materialsInIndex = $this->materialIndex->getAllIds();
        $allIds = $this->learningMaterialRepository->getFileLearningMaterialIds();
        return array_values(
            array_diff($allIds, $materialsInIndex)
        );
    }

    protected function checkSessions(): array
    {
        $sessionsInIndex = $this->curriculumIndex->getAllSessionIds();
        $allIds = $this->sessionRepository->getIds();
        return array_values(
            array_diff($allIds, $sessionsInIndex)
        );
    }

    protected function displayResults(
        StyleInterface $io,
        OutputInterface $output,
        array $missingMaterials,
        array $missingSessions
    ): int {
        if ($missingMaterials) {
            $io->title('Missing Materials (' . count($missingMaterials) . ')');
            $count = count($missingMaterials);
            $io->listing(array_slice($missingMaterials, 0, 10));
            if ($count > 10) {
                $io->warning('and ' . $count - 10 . ' additional materials.');
            }
        }

        $coursesWithMissingSessions = [];

        if ($missingSessions) {
            $coursesWithMissingSessions = array_reduce(
                $this->sessionRepository->getCoursesForSessionIds($missingSessions),
                function (array $carry, array $arr): array {
                    if (!array_key_exists($arr['courseId'], $carry)) {
                        $carry[$arr['courseId']] = [
                            'title' => $arr['courseTitle'],
                            'courseId' => $arr['courseId'],
                            'sessions' => [],
                        ];
                    }
                    $carry[$arr['courseId']]['sessions'][] = $arr['sessionId'];

                    return $carry;
                },
                []
            );
            $tableRows = array_map(function (array $item) {
                $count = count($item['sessions']);
                $sessions = implode(', ', array_slice($item['sessions'], 0, 10));
                if ($count > 10) {
                    $sessions .= ' and ' . $count - 10 . ' additional sessions';
                }

                return ["{$item['title']} ({$item['courseId']})", $sessions];
            }, $coursesWithMissingSessions);

            $count = count($tableRows);
            $io->title('Missing Sessions (' . count($missingSessions) . ')');
            $table = new Table($output);
            $table->setStyle('compact');
            $table->setHeaders(['Course', 'Missing Sessions']);
            $table->setRows(array_slice($tableRows, 0, 10));
            $table->render();
            if ($count > 10) {
                $io->warning('and ' . $count - 10 . ' additional courses.');
            }
        }

        $io->newLine();

        $reIndex =  $io->confirm('Would you like to index these missing items again?', true);

        if ($reIndex) {
            $this->reIndex($output, $missingMaterials, $coursesWithMissingSessions);
            return Command::SUCCESS;
        }

        return Command::FAILURE;
    }

    protected function reIndex(OutputInterface $output, array $materials, array $courses): void
    {
        $progressBar = new ProgressBar($output, count($materials) + count($courses));
        $progressBar->start();
        if ($materials) {
            $dtos = $this->learningMaterialRepository->findDTOsBy(['id' => $materials]);
            foreach ($dtos as $dto) {
                $this->materialIndex->index([$dto]);
                $progressBar->advance();
            }
        }
        if ($courses) {
            $ids = array_column($courses, 'courseId');
            foreach ($ids as $id) {
                $this->curriculumIndex->index([$id], new DateTime());
                $progressBar->advance();
            }
        }
        $progressBar->finish();
    }
}
