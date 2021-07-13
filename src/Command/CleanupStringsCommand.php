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
class CleanupStringsCommand extends Command
{
    protected HTMLPurifier $purifier;

    protected EntityManagerInterface $em;

    protected SessionObjectiveRepository $sessionObjectiveRepository;

    protected CourseObjectiveRepository $courseObjectiveRepository;

    protected ProgramYearObjectiveRepository $programYearObjectiveRepository;

    protected LearningMaterialRepository $learningMaterialRepository;

    protected CourseLearningMaterialRepository $courseLearningMaterialRepository;

    protected SessionLearningMaterialRepository $sessionLearningMaterialRepository;

    protected SessionRepository $sessionRepository;

    protected HttpClientInterface $httpClient;

    /**
     * @var int where to limit each query for memory management
     */
    private const QUERY_LIMIT = 500;

    public function __construct(
        HTMLPurifier $purifier,
        EntityManagerInterface $em,
        LearningMaterialRepository $learningMaterialRepository,
        CourseLearningMaterialRepository $courseLearningMaterialRepository,
        SessionLearningMaterialRepository $sessionLearningMaterialRepository,
        SessionRepository $sessionRepository,
        SessionObjectiveRepository $sessionObjectiveRepository,
        CourseObjectiveRepository $courseObjectiveRepository,
        ProgramYearObjectiveRepository $programYearObjectiveRepository,
        HttpClientInterface $httpClient
    ) {
        $this->purifier = $purifier;
        $this->em = $em;
        $this->learningMaterialRepository = $learningMaterialRepository;
        $this->courseLearningMaterialRepository = $courseLearningMaterialRepository;
        $this->sessionLearningMaterialRepository = $sessionLearningMaterialRepository;
        $this->sessionRepository = $sessionRepository;
        $this->sessionObjectiveRepository = $sessionObjectiveRepository;
        $this->courseObjectiveRepository = $courseObjectiveRepository;
        $this->programYearObjectiveRepository = $programYearObjectiveRepository;
        $this->httpClient = $httpClient;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:cleanup-strings')
            ->setAliases(['ilios:maintenance:cleanup-strings'])
            ->setDescription('Cleans up selected text fields in the database.')
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
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('objective-title')) {
            $this->purifyObjectiveTitle($output);
        }
        if ($input->getOption('learningmaterial-description')) {
            $this->purifyLearnignMaterialDescription($output);
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

        return 0;
    }

    /**
     * Purify objective titles
     * @param OutputInterface $output
     * @throws Exception
     */
    protected function purifyObjectiveTitle(OutputInterface $output)
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
            $this->programYearObjectiveRepository
        ];

        foreach ($objectiveManagers as $objectiveManager) {
            $offset = 0;
            do {
                $objectives = $objectiveManager->findBy([], ['id' => 'ASC'], $limit, $offset);
                foreach ($objectives as $objective) {
                    $originalTitle = $objective->getTitle();
                    $cleanTitle = $this->purifier->purify($originalTitle);
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
     * @param OutputInterface $output
     */
    protected function purifyLearnignMaterialDescription(OutputInterface $output)
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
     * @param OutputInterface $output
     */
    protected function purifyCourseLearningMaterialNote(OutputInterface $output)
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
     * @param OutputInterface $output
     */
    protected function purifySessionLearningMaterialNote(OutputInterface $output)
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
     * @param OutputInterface $output
     */
    protected function purifySessionDescription(OutputInterface $output)
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

    /**
     * @param OutputInterface $output
     */
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
            /* @var LearningMaterialInterface $material */
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
     * @param string $link
     * @return string|null
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
