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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Queues the updating of all indexed items
 */
class UpdateCommand extends Command
{
    public const COMMAND_NAME = 'ilios:index:update';

    public function __construct(
        protected UserRepository $userRepository,
        protected CourseRepository $courseRepository,
        protected MeshDescriptorRepository $descriptorRepository,
        protected LearningMaterialRepository $learningMaterialRepository,
        protected MessageBusInterface $bus,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Queue everything to be updated in the index.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->queueUsers($output);
        $this->queueLearningMaterials($output);
        $this->queueCourses($output);
        $this->queueMesh($output);

        return 0;
    }

    protected function queueUsers(OutputInterface $output)
    {
        $allIds = $this->userRepository->getIds();
        $count = count($allIds);
        $chunks = array_chunk($allIds, UserIndexRequest::MAX_USERS);
        foreach ($chunks as $ids) {
            $this->bus->dispatch(new UserIndexRequest($ids));
        }
        $output->writeln("<info>{$count} users have been queued for indexing.</info>");
    }

    protected function queueCourses(OutputInterface $output)
    {
        $allIds = $this->courseRepository->getIds();
        $count = count($allIds);
        $chunks = array_chunk($allIds, CourseIndexRequest::MAX_COURSES);
        foreach ($chunks as $ids) {
            $this->bus->dispatch(new CourseIndexRequest($ids));
        }
        $output->writeln("<info>{$count} courses have been queued for indexing.</info>");
    }

    protected function queueLearningMaterials(OutputInterface $output)
    {
        $allIds = $this->learningMaterialRepository->getFileLearningMaterialIds();
        $count = count($allIds);
        foreach ($allIds as $id) {
            $this->bus->dispatch(new LearningMaterialIndexRequest($id));
        }
        $output->writeln("<info>{$count} learning materials have been queued for indexing.</info>");
    }

    protected function queueMesh(OutputInterface $output)
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
