<?php

declare(strict_types=1);

namespace App\Command;

use App\Message\LearningMaterialTextExtractionRequest;
use App\Repository\LearningMaterialRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Queues extraction of learning material text
 */
#[AsCommand(
    name: 'ilios:extract-material-text',
    description: 'Extract text from learning materials'
)]
class ExtractLearningMaterialsTextCommand extends Command
{
    public function __construct(
        protected LearningMaterialRepository $learningMaterialRepository,
        protected MessageBusInterface $bus
    ) {
        parent::__construct();

        $this
            ->addOption(
                'materials',
                null,
                InputOption::VALUE_REQUIRED,
                'Comma-separated list list of materials to extract.',
            )
            ->addOption(
                'overwrite',
                null,
                InputOption::VALUE_NONE,
                'Overwrite existing text.',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $overwrite = $input->getOption('overwrite');
        $allIds = $this->learningMaterialRepository->getFileLearningMaterialIds();
        if ($input->getOption('materials')) {
            $ids = explode(',', $input->getOption('materials'));
            $allIds = array_intersect($allIds, $ids);
        }
        $count = count($allIds);
        $chunks = array_chunk($allIds, LearningMaterialTextExtractionRequest::MAX_MATERIALS);
        foreach ($chunks as $ids) {
            $this->bus->dispatch(new LearningMaterialTextExtractionRequest($ids, $overwrite));
        }
        $output->writeln("<info>{$count} learning materials have been queued for text extraction</info>");
        if ($overwrite) {
            $output->writeln('<comment>Existing text extractions for these materials will be overwritten</comment>');
        }

        return Command::SUCCESS;
    }
}
