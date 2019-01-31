<?php

namespace App\Command;

use App\Entity\Course;
use App\Entity\DTO\CourseDTO;
use App\Entity\DTO\UserDTO;
use App\Entity\Manager\CourseManager;
use App\Entity\Manager\UserManager;
use App\Entity\User;
use App\Service\Search;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Populates the search index with documents
 *
 * Class PopulateIndexCommand
 */
class PopulateIndexCommand extends Command
{
    /**
     * @var Search
     */
    protected $search;

    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var CourseManager
     */
    private $courseManager;


    public function __construct(
        Search $search,
        UserManager $userManager,
        CourseManager $courseManager
    ) {
        parent::__construct();

        $this->search = $search;
        $this->userManager = $userManager;
        $this->courseManager = $courseManager;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:populate-index')
            ->setDescription('Populate the search index with documents.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Clearing the index and preparing to insert data.</info>");
        $this->search->clear();
        $output->writeln("<info>Ok.</info>");
        $progressBar = new ProgressBar($output);
        ProgressBar::setFormatDefinition(
            'normal',
            "<info>%message%</info>\n%current%/%max% [%bar%]"
        );
        $this->populateUsers($output);
        $this->populateCourses($output);
        $output->writeln("");
        $output->writeln("Index Populated!");
    }

    protected function populateUsers(OutputInterface $output)
    {
        $allIds = $this->userManager->getIds();
        $progressBar = new ProgressBar($output, count($allIds));
        $progressBar->setMessage('Adding Users...');
        $progressBar->start();
        $chunks = array_chunk($allIds, 500);
        foreach ($chunks as $ids) {
            $dtos = $this->userManager->findDTOsBy(['id' => $ids]);
            $users = array_map(function (UserDTO $user) {
                return [
                    'id' => $user->id,
                    'firstName' => $user->firstName,
                    'lastName' => $user->lastName,
                    'middleName' => $user->middleName,
                    'email' => $user->email,
                    'campusId' => $user->campusId,
                    'username' => $user->username,
                ];
            }, $dtos);

            $this->search->bulkIndex(Search::PRIVATE_INDEX, User::class, $users);
            $progressBar->advance(count($ids));
        }
        $progressBar->setMessage(count($allIds) . " Users Added!");
        $progressBar->finish();
    }

    protected function populateCourses(OutputInterface $output)
    {
        $allIds = $this->courseManager->getIds();
        $progressBar = new ProgressBar($output, count($allIds));
        $progressBar->setMessage('Adding Courses...');
        $progressBar->start();
        $chunks = array_chunk($allIds, 500);
        foreach ($chunks as $ids) {
            $dtos = $this->courseManager->findDTOsBy(['id' => $ids]);
            $users = array_map(function (CourseDTO $course) {
                return [
                    'id' => $course->id,
                    'title' => $course->title,
                ];
            }, $dtos);

            $this->search->bulkIndex(Search::PUBLIC_INDEX, Course::class, $users);
            $progressBar->advance(count($ids));
        }
        $progressBar->setMessage(count($allIds) . " Courses Added!");
        $progressBar->finish();
    }
}
