<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\LearnerGroupRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Checks groups to ensure members are correctly provisioned at every level
 */
#[AsCommand(
    name: 'ilios:check-group-sets',
    description: 'Checks groups to ensure members are correctly provisioned at every level.'
)]
class CheckGroupSets extends Command
{
    public function __construct(
        protected LearnerGroupRepository $learnerGroupRepository,
    ) {
        parent::__construct();
    }

    public function __invoke(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->info('Searching groups and trees, this can take a while...');
        $errors = $this->learnerGroupRepository->findErrorsInGroupTrees();


        if (!count($errors)) {
            $io->success('All groups have been correctly provisioned.');
            return Command::SUCCESS;
        }

        $io->error('Errors found: ' . count($errors));

        $errorMessages = array_map(fn($error) => [
            $error['id'], $error['parentId'], count($error['missingFromParent']),
        ], $errors);

        $table = new Table($output);
        $table
            ->setHeaders(['Group', 'Parent Group', 'Missing Users'])
            ->setRows($errorMessages);
        $table->render();


        return Command::FAILURE;
    }
}
