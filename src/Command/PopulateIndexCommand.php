<?php

namespace App\Command;

use App\Entity\Course;
use App\Entity\DTO\CourseDTO;
use App\Entity\Manager\CourseManager;
use App\Entity\Manager\MeshDescriptorManager;
use App\Entity\Manager\UserManager;
use App\Service\Index;
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
     * @var Index
     */
    protected $index;

    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var CourseManager
     */
    protected $courseManager;

    /**
     * @var MeshDescriptorManager
     */
    protected $descriptorManager;


    public function __construct(
        Index $index,
        UserManager $userManager,
        CourseManager $courseManager,
        MeshDescriptorManager $descriptorManager
    ) {
        parent::__construct();

        $this->index = $index;
        $this->userManager = $userManager;
        $this->courseManager = $courseManager;
        $this->descriptorManager = $descriptorManager;
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
        $this->index->clear();
        $output->writeln("<info>Ok.</info>");
        ProgressBar::setFormatDefinition(
            'normal',
            "<info>%message%</info>\n%current%/%max% [%bar%]"
        );
        $this->populateUsers($output);
        $this->populateCourses($output);
        $this->populateMesh($output);
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
            $this->index->indexUsers($dtos);
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
            $this->index->indexCourses($dtos);
            $progressBar->advance(count($ids));
        }
        $progressBar->setMessage(count($allIds) . " Courses Added!");
        $progressBar->finish();
    }

    protected function populateMesh(OutputInterface $output)
    {
        $allIds = $this->descriptorManager->getIds();
        $progressBar = new ProgressBar($output, count($allIds));
        $progressBar->setMessage('Adding MeSH...');
        $progressBar->start();
        $chunks = array_chunk($allIds, 500);
        foreach ($chunks as $ids) {
            $descriptors = $this->descriptorManager->getIliosMeshDescriptorsById($ids);
            $this->index->indexMeshDescriptors($descriptors);
            $progressBar->advance(count($ids));
        }
        $progressBar->setMessage(count($allIds) . " Descriptors Added!");
        $progressBar->finish();
    }
}
