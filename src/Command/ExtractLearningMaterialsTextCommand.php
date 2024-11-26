<?php

declare(strict_types=1);

namespace App\Command;

use App\Message\LearningMaterialTextExtractionRequest;
use App\Repository\LearningMaterialRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Queues extraction of learning material text
 */
#[AsCommand(
    name: 'ilios:extract-material-text',
    description: 'Queue extraction of learning material text.'
)]
class ExtractLearningMaterialsTextCommand extends Command
{
    public function __construct(
        protected LearningMaterialRepository $learningMaterialRepository,
        protected MessageBusInterface $bus
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $allIds = $this->learningMaterialRepository->getFileLearningMaterialIds();
        $count = count($allIds);
        $chunks = array_chunk($allIds, LearningMaterialTextExtractionRequest::MAX_MATERIALS);
        foreach ($chunks as $ids) {
            $this->bus->dispatch(new LearningMaterialTextExtractionRequest($ids));
        }
        $output->writeln("<info>{$count} learning materials have been queued for text extraction.</info>");

        return Command::SUCCESS;
    }
}
