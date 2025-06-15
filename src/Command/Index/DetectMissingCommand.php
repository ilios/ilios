<?php

declare(strict_types=1);

namespace App\Command\Index;

use App\Repository\CourseRepository;
use App\Repository\LearningMaterialRepository;
use App\Repository\SessionRepository;
use App\Service\Index\Curriculum;
use App\Service\Index\LearningMaterials;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ilios:index:detect-missing',
    description: 'Find items that are missing in the index'
)]
class DetectMissingCommand extends Command
{
    public function __construct(
        protected readonly LearningMaterialRepository $learningMaterialRepository,
        protected readonly CourseREpository $courseRepository,
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

        if ($missingMaterials) {
            $io->title('Missing Materials (' . count($missingMaterials) . ')');
            $io->listing($missingMaterials);
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
            $io->title('Missing Sessions (' . count($missingSessions) . ')');
            $table = new Table($output);
            $table->setStyle('compact');
            $table->setHeaders(['Course', 'Missing Sessions']);
            $table->setRows($tableRows);
            $table->render();
        }

        return Command::FAILURE;
    }

    protected function checkMaterials(): array
    {
        $materialsInIndex = $this->materialIndex->getAllIds();
        $allIds = $this->learningMaterialRepository->getFileLearningMaterialIds();
        return array_diff($allIds, $materialsInIndex);
    }

    protected function checkSessions(): array
    {
        $sessionsInIndex = $this->curriculumIndex->getAllSessionIds();
        $allIds = $this->sessionRepository->getIds();
        return array_diff($allIds, $sessionsInIndex);
    }
}
