<?php

declare(strict_types=1);

namespace App\Command\Index;

use App\Repository\LearningMaterialRepository;
use App\Service\Index\LearningMaterials;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ilios:index:detect-missing',
    description: 'Find items that are missing in the index'
)]
class DetectMissingCommand extends Command
{
    public function __construct(
        protected LearningMaterialRepository $learningMaterialRepository,
        protected LearningMaterials $materialIndex,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->materialIndex->isEnabled()) {
            $output->writeln("<comment>Indexing is not currently configured.</comment>");
            return Command::FAILURE;
        }
        $materialsInIndex = $this->materialIndex->getAllIds();
        $allIds = $this->learningMaterialRepository->getFileLearningMaterialIds();
        $missing = array_diff($allIds, $materialsInIndex);
        $count = count($missing);
        if ($count) {
            $list = implode(', ', $missing);
            $output->writeln("<error>{$count} materials are missing from the index.</error>");
            $output->writeln("<comment>Materials: {$list}</comment>");
            return Command::FAILURE;
        }

        $output->writeln("<info>All materials are indexed.</info>");

        return Command::SUCCESS;
    }
}
