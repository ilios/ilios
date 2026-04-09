<?php

declare(strict_types=1);

namespace App\Command\Index;

use App\Repository\LearningMaterialRepository;
use App\Service\Config;
use App\Service\Index\LearningMaterials;
use Composer\Console\Input\InputArgument;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'ilios:index:material',
    description: 'Extract and index a material by id'
)]
class MaterialCommand extends Command
{
    public function __construct(
        protected LearningMaterialRepository $learningMaterialRepository,
        protected LearningMaterials $index,
        protected readonly Config $config,
    ) {
        parent::__construct();
    }

    public function __invoke(
        OutputInterface $output,
        #[Argument(description: 'The ID of the material to index.', name: 'materialId')] int $id,
    ): int {
        if (!$this->index->isEnabled()) {
            $output->writeln("<comment>Indexing is not currently configured.</comment>");
            return Command::FAILURE;
        }
        if ($this->config->get('learningMaterialsDisabled')) {
            $output->writeln("<comment>Learning Materials are disabled on this instance.</comment>");
            return Command::FAILURE;
        }
        $dto = $this->learningMaterialRepository->findDTOBy(['id' => $id]);
        $this->index->index([$dto], true);

        return Command::SUCCESS;
    }
}
