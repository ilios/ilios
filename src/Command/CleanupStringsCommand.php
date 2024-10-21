<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\LearningMaterialInterface;
use App\Repository\CourseLearningMaterialRepository;
use App\Repository\CourseObjectiveRepository;
use App\Repository\LearningMaterialRepository;
use App\Repository\ProgramYearObjectiveRepository;
use App\Repository\SessionLearningMaterialRepository;
use App\Repository\SessionObjectiveRepository;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use HTMLPurifier;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Cleans up all the strings in the database
 *
 * Class CleanupStringsCommand
 */
#[AsCommand(
    name: 'ilios:cleanup-strings',
    description: 'Cleans up selected text fields in the database.',
    aliases: ['ilios:maintenance:cleanup-strings']
)]
class CleanupStringsCommand extends Command
{
    /**
     * @var int where to limit each query for memory management
     */
    private const QUERY_LIMIT = 500;

    private const CLEANUP_MODE_OBJECTIVE_TITLE_TRIM_BLANK_SPACE = 1;

    private const CLEANUP_MODE_OBJECTIVE_TITLE_PURIFY_MARKUP = 2;

    public function __construct(
        protected HTMLPurifier $purifier,
        protected EntityManagerInterface $em,
        protected LearningMaterialRepository $learningMaterialRepository,
        protected CourseLearningMaterialRepository $courseLearningMaterialRepository,
        protected SessionLearningMaterialRepository $sessionLearningMaterialRepository,
        protected SessionRepository $sessionRepository,
        protected SessionObjectiveRepository $sessionObjectiveRepository,
        protected CourseObjectiveRepository $courseObjectiveRepository,
        protected ProgramYearObjectiveRepository $programYearObjectiveRepository,
        protected HttpClientInterface $httpClient
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'objective-title',
                null,
                InputOption::VALUE_NONE,
                'Should we purify the markup of objective titles?'
            )
            ->addOption(
                'learningmaterial-description',
                null,
                InputOption::VALUE_NONE,
                'Should we purify the markup of learning material descriptions?'
            )
            ->addOption(
                'learningmaterial-note',
                null,
                InputOption::VALUE_NONE,
                'Should we purify the markup of learning materials notes?'
            )
            ->addOption(
                'session-description',
                null,
                InputOption::VALUE_NONE,
                'Should we purify the markup of session descriptions?'
            )
            ->addOption(
                'learningmaterial-links',
                null,
                InputOption::VALUE_NONE,
                'Should we attempt to correct learning material links?'
            )
            ->addOption(
                'objective-title-blankspace',
                null,
                InputOption::VALUE_NONE,
                'Should we remove leading and trailing blank space from objective titles?'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('objective-title')) {
            $this->purifyObjectiveTitle($output);
        }
        if ($input->getOption('learningmaterial-description')) {
            $this->purifyLearningMaterialDescription($output);
        }
        if ($input->getOption('learningmaterial-note')) {
            $this->purifyCourseLearningMaterialNote($output);
            $output->writeln('');
            $this->purifySessionLearningMaterialNote($output);
        }
        if ($input->getOption('session-description')) {
            $this->purifySessionDescription($output);
        }
        if ($input->getOption('learningmaterial-links')) {
            $this->correctLearningMaterialLinks($output);
        }
        if ($input->getOption('objective-title-blankspace')) {
            $this->removeObjectiveTitleBlankSpace($output);
        }

        return Command::SUCCESS;
    }

    /**
     * Purify objective titles
     * @throws Exception
     */
    protected function purifyObjectiveTitle(OutputInterface $output): void
    {
        $this->cleanupObjectiveTitle($output, self::CLEANUP_MODE_OBJECTIVE_TITLE_PURIFY_MARKUP);
    }

    /**
     * Removes leading and trailing blank space from objective titles.
     * @throws Exception
     */
    protected function removeObjectiveTitleBlankSpace(OutputInterface $output): void
    {
        $this->cleanupObjectiveTitle($output, self::CLEANUP_MODE_OBJECTIVE_TITLE_TRIM_BLANK_SPACE);
    }

    protected function cleanupObjectiveTitle(OutputInterface $output, int $mode): void
    {
        $cleanedTitles = 0;
        $limit = self::QUERY_LIMIT;
        $totalObjectives = $this->sessionObjectiveRepository->getTotalObjectiveCount();
        $totalObjectives += $this->courseObjectiveRepository->getTotalObjectiveCount();
        $totalObjectives += $this->programYearObjectiveRepository->getTotalObjectiveCount();

        $progress = new ProgressBar($output, $totalObjectives);
        $progress->setRedrawFrequency(208);
        $output->writeln("<info>Starting cleanup of objective titles...</info>");
        $progress->start();
        $objectiveManagers = [
            $this->sessionObjectiveRepository,
            $this->courseObjectiveRepository,
            $this->programYearObjectiveRepository,
        ];

        foreach ($objectiveManagers as $objectiveManager) {
            $offset = 0;
            do {
                $objectives = $objectiveManager->findBy([], ['id' => 'ASC'], $limit, $offset);
                foreach ($objectives as $objective) {
                    $originalTitle = $objective->getTitle();
                    if (self::CLEANUP_MODE_OBJECTIVE_TITLE_TRIM_BLANK_SPACE  === $mode) {
                        $cleanTitle = trim($originalTitle);
                    } else {
                        $cleanTitle = $this->purifier->purify($originalTitle);
                    }
                    if ($originalTitle != $cleanTitle) {
                        $cleanedTitles++;
                        $objective->setTitle($cleanTitle);
                        $objectiveManager->update($objective, false);
                    }
                    $progress->advance();
                }

                $offset += $limit;
                $this->em->flush();
                $this->em->clear();
            } while (count($objectives) === $limit);
        }

        $progress->finish();
        $output->writeln('');
        $output->writeln("<info>{$cleanedTitles} Objective Titles updated.</info>");
    }

    /**
     * Purify learning material description
     */
    protected function purifyLearningMaterialDescription(OutputInterface $output): void
    {
        $cleaned = 0;
        $offset = 0;
        $limit = self::QUERY_LIMIT;
        $total = $this->learningMaterialRepository->getTotalLearningMaterialCount();
        $progress = new ProgressBar($output, $total);
        $progress->setRedrawFrequency(208);
        $output->writeln("<info>Starting cleanup of learning material description...</info>");
        $progress->start();
        do {
            $materials = $this->learningMaterialRepository
                ->findBy([], ['id' => 'ASC'], $limit, $offset);
            foreach ($materials as $material) {
                $original = $material->getDescription();
                $clean = $this->purifier->purify($original);
                if ($original != $clean) {
                    $cleaned++;
                    $material->setDescription($clean);
                    $this->learningMaterialRepository->update($material, false);
                }
                $progress->advance();
            }

            $offset += $limit;
            $this->em->flush();
            $this->em->clear();
        } while (count($materials) === $limit);
        $progress->finish();
        $output->writeln('');
        $output->writeln("<info>{$cleaned} Learning Material Descriptions updated.</info>");
    }

    /**
     * Purify course learning material note
     */
    protected function purifyCourseLearningMaterialNote(OutputInterface $output): void
    {
        $cleaned = 0;
        $offset = 0;
        $limit = self::QUERY_LIMIT;
        $total = $this->courseLearningMaterialRepository->getTotalCourseLearningMaterialCount();
        $progress = new ProgressBar($output, $total);
        $progress->setRedrawFrequency(208);
        $output->writeln("<info>Starting cleanup of course learning material notes...</info>");
        $progress->start();
        do {
            $materials = $this->courseLearningMaterialRepository->findBy([], ['id' => 'ASC'], $limit, $offset);
            foreach ($materials as $material) {
                $original = $material->getNotes();
                $clean = $this->purifier->purify($original);
                if ($original != $clean) {
                    $cleaned++;
                    $material->setNotes($clean);
                    $this->courseLearningMaterialRepository->update($material, false);
                }
                $progress->advance();
            }

            $offset += $limit;
            $this->em->flush();
            $this->em->clear();
        } while (count($materials) === $limit);
        $progress->finish();
        $output->writeln('');
        $output->writeln("<info>{$cleaned} Course Learning Material Notes updated.</info>");
    }

    /**
     * Purify session learning material note
     */
    protected function purifySessionLearningMaterialNote(OutputInterface $output): void
    {
        $cleaned = 0;
        $offset = 0;
        $limit = self::QUERY_LIMIT;
        $total = $this->sessionLearningMaterialRepository->getTotalSessionLearningMaterialCount();
        $progress = new ProgressBar($output, $total);
        $progress->setRedrawFrequency(208);
        $output->writeln("<info>Starting cleanup of session learning material notes...</info>");
        $progress->start();
        do {
            $materials = $this->sessionLearningMaterialRepository->findBy([], ['id' => 'ASC'], $limit, $offset);
            foreach ($materials as $material) {
                $original = $material->getNotes();
                $clean = $this->purifier->purify($original);
                if ($original != $clean) {
                    $cleaned++;
                    $material->setNotes($clean);
                    $this->sessionLearningMaterialRepository->update($material, false);
                }
                $progress->advance();
            }

            $offset += $limit;
            $this->em->flush();
            $this->em->clear();
        } while (count($materials) === $limit);
        $progress->finish();
        $output->writeln('');
        $output->writeln("<info>{$cleaned} Session Learning Material Notes updated.</info>");
    }

    /**
     * Purify session description
     */
    protected function purifySessionDescription(OutputInterface $output): void
    {
        $cleaned = 0;
        $offset = 0;
        $limit = self::QUERY_LIMIT;
        $total = $this->sessionRepository->getTotalSessionCount();
        $progress = new ProgressBar($output, $total);
        $progress->setRedrawFrequency(208);
        $output->writeln("<info>Starting cleanup of session descriptions...</info>");
        $progress->start();
        do {
            $sessions = $this->sessionRepository->findBy([], ['id' => 'ASC'], $limit, $offset);
            foreach ($sessions as $session) {
                $original = $session->getDescription();
                $clean = $this->purifier->purify($original);
                if ($original != $clean) {
                    $cleaned++;
                    $session->setDescription($clean);
                    $this->sessionRepository->update($session, false);
                }
                $progress->advance();
            }

            $offset += $limit;
            $this->em->flush();
            $this->em->clear();
        } while (count($sessions) === $limit);
        $progress->finish();
        $output->writeln('');
        $output->writeln("<info>{$cleaned} Session Descriptions updated.</info>");
    }

    public function correctLearningMaterialLinks(OutputInterface $output): void
    {
        $cleaned = 0;
        $offset = 0;
        $failures = [];
        $limit = self::QUERY_LIMIT;
        $total = $this->learningMaterialRepository->getTotalLearningMaterialCount();
        $progress = new ProgressBar($output, $total);
        $progress->setRedrawFrequency(208);
        $output->writeln("<info>Starting cleanup of learning material links...</info>");
        $progress->start();
        do {
            $materials = $this->learningMaterialRepository->findBy([], ['id' => 'ASC'], $limit, $offset);
            /** @var LearningMaterialInterface $material */
            foreach ($materials as $material) {
                $original = $material->getLink();
                if (null === $original || '' === trim($original)) {
                    continue;
                }
                try {
                    $fixed = $this->fixLink($original);
                    if ($original !== $fixed) {
                        $cleaned++;
                        $material->setLink($fixed);
                        $this->learningMaterialRepository->update($material, false);
                    }
                } catch (Exception $e) {
                    $failures[] = [$material->getId(), $original, $e->getMessage()];
                }
                $progress->advance();
            }

            $offset += $limit;
            $this->em->flush();
            $this->em->clear();
        } while (count($materials) === $limit);
        $progress->finish();
        $output->writeln('');
        $output->writeln("<info>{$cleaned} learning material links updated, " . count($failures) . " failures.</info>");
        $output->writeln('');
        if (! empty($failures) && $output->isVerbose()) {
            $table = new Table($output);
            $table->setHeaders(['Learning Material ID', 'Link', 'Error Message']);
            $table->setRows($failures);
            $table->render();
        }
    }

    /**
     * @throws TransportExceptionInterface
     */
    protected function fixLink(string $link): ?string
    {
        $fixed = trim($link);
        $fixed = str_ireplace(
            ['http://https://', 'http://http://', 'http://ftps://', 'http://ftp://'],
            ['https://', 'http://', 'ftps://', 'ftp://'],
            $fixed
        );
        if (preg_match(';^(http|ftp)s?://;i', $fixed)) {
            return $fixed;
        }

        // attempt to fetch a response over HTTPS first.
        $url = 'https://' . $fixed;
        try {
            $this->httpClient->request('HEAD', $url);
        } catch (Exception) {
            // fallback - try getting a response over plain HTTP.
            $url = 'http://' . $fixed;
            $this->httpClient->request('HEAD', $url);
        }
        return $url;
    }
}
