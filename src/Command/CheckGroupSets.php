<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\LearnerGroupRepository;
use App\Repository\UserRepository;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
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
        protected UserRepository $userRepository,
    ) {
        parent::__construct();
    }

    public function __invoke(
        InputInterface $input,
        OutputInterface $output,
        #[Option(
            description: 'Automatically fix errors by adding missing users to parent groups.',
            name: 'fix'
        )] bool $fix = false,
    ): int {
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

        if (!$fix) {
            $errorCount = count($errors);
            $fix = $io->confirm(
                "Found {$errorCount} error(s). Auto-fix by adding missing users to parent groups?",
                false
            );
        }

        if (!$fix) {
            return Command::FAILURE;
        }

        $this->fixErrors($io, $errors);

        return Command::SUCCESS;
    }

    protected function fixErrors(SymfonyStyle $io, array $errors): void
    {
        $io->newLine();
        $io->text('Fixing group errors...');
        $io->progressStart(count($errors));

        $fixedUsers = 0;
        $fixedGroups = 0;

        foreach (array_chunk($errors, 25) as $chunk) {
            foreach ($chunk as $error) {
                $fixedUsers += $this->addMissingUsersToGroup(
                    $error['parentId'],
                    $error['missingFromParent']
                );
                $fixedGroups++;
                $io->progressAdvance();
            }

            $this->learnerGroupRepository->flushAndClear();
        }

        $io->progressFinish();
        $io->success(
            "Added {$fixedUsers} missing user(s) across {$fixedGroups} parent group(s)."
        );
    }

    protected function addMissingUsersToGroup(int $groupId, array $missingUsers): int
    {
        $group = $this->learnerGroupRepository->find($groupId);
        if (!$group) {
            throw new RuntimeException(
                "Unable to find parent learner group #{$groupId}."
            );
        }

        $added = 0;
        foreach ($missingUsers as $userId) {
            $user = $this->userRepository->find($userId);
            if ($user) {
                $group->addUser($user);
                $added++;
            }
        }

        $this->learnerGroupRepository->update($group, false);

        return $added;
    }
}
