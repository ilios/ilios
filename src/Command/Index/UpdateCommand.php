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
use Doctrine\ORM\EntityManagerInterface;
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
        protected EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->queueUsers($output);
        //temporarily disable LM indexing for performance reasons.
//        $this->queueLearningMaterials($output);
        $this->queueCourses($output);
        $this->queueMesh($output);

        return Command::SUCCESS;
    }

    protected function queueUsers(OutputInterface $output): void
    {
        $clearedCount = $this->clearMessages('UserIndexRequest');
        $allIds = $this->userRepository->getIds();
        $count = count($allIds);
        $chunks = array_chunk($allIds, UserIndexRequest::MAX_USERS);
        foreach ($chunks as $ids) {
            $this->bus->dispatch(new UserIndexRequest($ids));
        }
        $this->confirmationMessage($output, $clearedCount, $count, 'users');
    }

    protected function queueCourses(OutputInterface $output): void
    {
        $clearedCount = $this->clearMessages('CourseIndexRequest');
        $allIds = $this->courseRepository->getIds();
        $count = count($allIds);
        $chunks = array_chunk($allIds, CourseIndexRequest::MAX_COURSES);
        foreach ($chunks as $ids) {
            $this->bus->dispatch(new CourseIndexRequest($ids));
        }
        $this->confirmationMessage($output, $clearedCount, $count, 'courses');
    }

    protected function queueLearningMaterials(OutputInterface $output): void
    {
        $clearedCount = $this->clearMessages('LearningMaterialIndexRequest');
        $allIds = $this->learningMaterialRepository->getFileLearningMaterialIds();
        $count = count($allIds);
        foreach ($allIds as $id) {
            $this->bus->dispatch(new LearningMaterialIndexRequest($id));
        }
        $this->confirmationMessage($output, $clearedCount, $count, 'learning materials');
    }

    protected function queueMesh(OutputInterface $output): void
    {
        $clearedCount = $this->clearMessages('MeshDescriptorIndexRequest');
        $allIds = $this->descriptorRepository->getIds();
        $count = count($allIds);
        $chunks = array_chunk($allIds, MeshDescriptorIndexRequest::MAX_DESCRIPTORS);
        foreach ($chunks as $ids) {
            $this->bus->dispatch(new MeshDescriptorIndexRequest($ids));
        }

        $this->confirmationMessage($output, $clearedCount, $count, 'MeSH descriptors');
    }

    protected function clearMessages(string $type): int
    {
        $connection = $this->em->getConnection();
        $stmt = $connection->prepare('DELETE FROM messenger_messages WHERE body LIKE :type');
        $stmt->bindValue("type", "%{$type}%");
        $results = $stmt->executeQuery();
        return $results->rowCount();
    }

    protected function confirmationMessage(
        OutputInterface $output,
        int $clearedCount,
        int $newRecords,
        string $type,
    ): void {
        $message = '';
        if ($clearedCount) {
            $message .= "Existing {$type} have been removed from the queue and ";
        }
        $output->writeln("<info>{$message}{$newRecords} {$type} have been queued for indexing.</info>");
    }
}
