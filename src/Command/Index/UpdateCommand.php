<?php

declare(strict_types=1);

namespace App\Command\Index;

use App\Message\CourseIndexRequest;
use App\Message\LearningMaterialIndexRequest;
use App\Message\MeshDescriptorIndexRequest;
use App\Message\UserIndexRequest;
use App\Repository\CourseRepository;
use App\Repository\LearningMaterialRepository;
use App\Repository\MeshDescriptorRepository;
use App\Repository\UserRepository;
use App\Service\Config;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Queues the updating of all indexed items
 */
#[AsCommand(
    name: 'ilios:index:update',
    description: 'Queue everything to be updated in the index.'
)]
class UpdateCommand extends Command
{
    public function __construct(
        protected UserRepository $userRepository,
        protected CourseRepository $courseRepository,
        protected MeshDescriptorRepository $descriptorRepository,
        protected LearningMaterialRepository $learningMaterialRepository,
        protected MessageBusInterface $bus,
        protected readonly Config $config,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->queueUsers($output);
        $this->queueLearningMaterials($output);
        $this->queueCourses($output);
        $this->queueMesh($output);

        return Command::SUCCESS;
    }

    protected function queueUsers(OutputInterface $output): void
    {
        $allIds = $this->userRepository->getIds();
        $count = count($allIds);
        $chunks = array_chunk($allIds, UserIndexRequest::MAX_USERS);
        foreach ($chunks as $ids) {
            $this->bus->dispatch(new UserIndexRequest($ids));
        }
        $output->writeln("<info>{$count} users have been queued for indexing.</info>");
    }

    protected function queueCourses(OutputInterface $output): void
    {
        $allIds = $this->courseRepository->getIds();
        $count = count($allIds);
        $chunks = array_chunk($allIds, CourseIndexRequest::MAX_COURSES);
        foreach ($chunks as $ids) {
            $this->bus->dispatch(new CourseIndexRequest($ids));
        }
        $output->writeln("<info>{$count} courses have been queued for indexing.</info>");
    }

    protected function queueLearningMaterials(OutputInterface $output): void
    {
        if ($this->config->get('learningMaterialsDisabled')) {
            $output->writeln("<info>Learning Materials are disabled on this instance.</info>");
            return;
        }
        $allIds = $this->learningMaterialRepository->getFileLearningMaterialIds();
        $count = count($allIds);
        $chunks = array_chunk($allIds, LearningMaterialIndexRequest::MAX_MATERIALS);
        foreach ($chunks as $ids) {
            $this->bus->dispatch(new LearningMaterialIndexRequest($ids));
        }
        $output->writeln("<info>{$count} learning materials have been queued for indexing.</info>");
    }

    protected function queueMesh(OutputInterface $output): void
    {
        $allIds = $this->descriptorRepository->getIds();
        $count = count($allIds);
        $chunks = array_chunk($allIds, MeshDescriptorIndexRequest::MAX_DESCRIPTORS);
        foreach ($chunks as $ids) {
            $this->bus->dispatch(new MeshDescriptorIndexRequest($ids));
        }
        $output->writeln("<info>{$count} descriptors have been queued for indexing.</info>");
    }
}
