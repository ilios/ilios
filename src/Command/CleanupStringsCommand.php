<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Manager\CourseObjectiveManager;
use App\Entity\Manager\ProgramYearObjectiveManager;
use App\Entity\Manager\SessionObjectiveManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use HTMLPurifier;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use App\Entity\Manager\LearningMaterialManager;
use App\Entity\Manager\CourseLearningMaterialManager;
use App\Entity\Manager\SessionLearningMaterialManager;
use App\Entity\Manager\SessionDescriptionManager;

/**
 * Cleans up all the strings in the database
 *
 * Class CleanupStringsCommand
 */
class CleanupStringsCommand extends Command
{
    /**
     * @var HTMLPurifier
     */
    protected $purifier;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var SessionObjectiveManager
     */
    protected $sessionObjectiveManager;

    /**
     * @var CourseObjectiveManager
     */
    protected $courseObjectiveManager;

    /**
     * @var ProgramYearObjectiveManager
     */
    protected $programYearObjectiveManager;

    /**
     * @var LearningMaterialManager
     */
    protected $learningMaterialManager;


    /**
     * @var CourseLearningMaterialManager
     */
    protected $courseLearningMaterialManager;


    /**
     * @var SessionLearningMaterialManager
     */
    protected $sessionLearningMaterialManager;


    /**
     * @var SessionDescriptionManager
     */
    protected $sessionDescriptionManager;

    /**
     * @var int where to limit each query for memory management
     */
    private const QUERY_LIMIT = 500;

    public function __construct(
        HTMLPurifier $purifier,
        EntityManagerInterface $em,
        LearningMaterialManager $learningMaterialManager,
        CourseLearningMaterialManager $courseLearningMaterialManager,
        SessionLearningMaterialManager $sessionLearningMaterialManager,
        SessionDescriptionManager $sessionDescriptionManager,
        SessionObjectiveManager $sessionObjectiveManager,
        CourseObjectiveManager $courseObjectiveManager,
        ProgramYearObjectiveManager $programYearObjectiveManager
    ) {
        $this->purifier = $purifier;
        $this->em = $em;
        $this->learningMaterialManager = $learningMaterialManager;
        $this->courseLearningMaterialManager = $courseLearningMaterialManager;
        $this->sessionLearningMaterialManager = $sessionLearningMaterialManager;
        $this->sessionDescriptionManager = $sessionDescriptionManager;
        $this->sessionObjectiveManager = $sessionObjectiveManager;
        $this->courseObjectiveManager = $courseObjectiveManager;
        $this->programYearObjectiveManager = $programYearObjectiveManager;

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
            ->setDescription('Purify HTML strings in the database to only contain allowed elements')
            ->addOption(
                'objective-title',
                null,
                InputOption::VALUE_NONE,
                'Should we update the title for objectives?'
            )
            ->addOption(
                'learningmaterial-description',
                null,
                InputOption::VALUE_NONE,
                'Should we update the description for learning materials?'
            )
            ->addOption(
                'learningmaterial-note',
                null,
                InputOption::VALUE_NONE,
                'Should we update the notes for learning materials?'
            )
            ->addOption(
                'session-description',
                null,
                InputOption::VALUE_NONE,
                'Should we update the description for sessions?'
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
        $totalObjectives = $this->sessionObjectiveManager->getTotalObjectiveCount();
        $totalObjectives += $this->courseObjectiveManager->getTotalObjectiveCount();
        $totalObjectives += $this->programYearObjectiveManager->getTotalObjectiveCount();

        $progress = new ProgressBar($output, $totalObjectives);
        $progress->setRedrawFrequency(208);
        $output->writeln("<info>Starting cleanup of objective titles...</info>");
        $progress->start();
        $objectiveManagers = [
            $this->sessionObjectiveManager,
            $this->courseObjectiveManager,
            $this->programYearObjectiveManager
        ];

        foreach ($objectiveManagers as $objectiveManager) {
            $offset = 1;
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
            } while (count($objectives) == $limit);
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
        $offset = 1;
        $limit = self::QUERY_LIMIT;
        $total = $this->learningMaterialManager->getTotalLearningMaterialCount();
        $progress = new ProgressBar($output, $total);
        $progress->setRedrawFrequency(208);
        $output->writeln("<info>Starting cleanup of learning material description...</info>");
        $progress->start();
        do {
            $materials = $this->learningMaterialManager
                ->findBy([], ['id' => 'ASC'], $limit, $offset);
            foreach ($materials as $material) {
                $original = $material->getDescription();
                $clean = $this->purifier->purify($original);
                if ($original != $clean) {
                    $cleaned++;
                    $material->setDescription($clean);
                    $this->learningMaterialManager->update($material, false);
                }
                $progress->advance();
            }

            $offset += $limit;
            $this->em->flush();
            $this->em->clear();
        } while (count($materials) == $limit);
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
        $offset = 1;
        $limit = self::QUERY_LIMIT;
        $total = $this->courseLearningMaterialManager->getTotalCourseLearningMaterialCount();
        $progress = new ProgressBar($output, $total);
        $progress->setRedrawFrequency(208);
        $output->writeln("<info>Starting cleanup of course learning material notes...</info>");
        $progress->start();
        do {
            $materials = $this->courseLearningMaterialManager->findBy([], ['id' => 'ASC'], $limit, $offset);
            foreach ($materials as $material) {
                $original = $material->getNotes();
                $clean = $this->purifier->purify($original);
                if ($original != $clean) {
                    $cleaned++;
                    $material->setNotes($clean);
                    $this->courseLearningMaterialManager->update($material, false);
                }
                $progress->advance();
            }

            $offset += $limit;
            $this->em->flush();
            $this->em->clear();
        } while (count($materials) == $limit);
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
        $offset = 1;
        $limit = self::QUERY_LIMIT;
        $total = $this->sessionLearningMaterialManager->getTotalSessionLearningMaterialCount();
        $progress = new ProgressBar($output, $total);
        $progress->setRedrawFrequency(208);
        $output->writeln("<info>Starting cleanup of session learning material notes...</info>");
        $progress->start();
        do {
            $materials = $this->sessionLearningMaterialManager->findBy([], ['id' => 'ASC'], $limit, $offset);
            foreach ($materials as $material) {
                $original = $material->getNotes();
                $clean = $this->purifier->purify($original);
                if ($original != $clean) {
                    $cleaned++;
                    $material->setNotes($clean);
                    $this->sessionLearningMaterialManager->update($material, false);
                }
                $progress->advance();
            }

            $offset += $limit;
            $this->em->flush();
            $this->em->clear();
        } while (count($materials) == $limit);
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
        $offset = 1;
        $limit = self::QUERY_LIMIT;
        $total = $this->sessionDescriptionManager->getTotalSessionDescriptionCount();
        $progress = new ProgressBar($output, $total);
        $progress->setRedrawFrequency(208);
        $output->writeln("<info>Starting cleanup of session descriptions...</info>");
        $progress->start();
        do {
            $descriptions = $this->sessionDescriptionManager->findBy([], ['id' => 'ASC'], $limit, $offset);
            foreach ($descriptions as $description) {
                $original = $description->getDescription();
                $clean = $this->purifier->purify($original);
                if ($original != $clean) {
                    $cleaned++;
                    $description->setDescription($clean);
                    $this->sessionDescriptionManager->update($description, false);
                }
                $progress->advance();
            }

            $offset += $limit;
            $this->em->flush();
            $this->em->clear();
        } while (count($descriptions) == $limit);
        $progress->finish();
        $output->writeln('');
        $output->writeln("<info>{$cleaned} Session Descriptions updated.</info>");
    }
}
