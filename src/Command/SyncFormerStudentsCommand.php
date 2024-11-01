<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\UserRoleInterface;
use App\Repository\UserRepository;
use App\Repository\UserRoleRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use App\Entity\UserInterface;
use App\Service\Directory;

/**
 * Syncs former students from the directory.
 *
 * Class SyncFormerStudentsCommand
 */
#[AsCommand(
    name: 'ilios:sync-former-students',
    description: 'Sync former students from the directory.',
    aliases: ['ilios:directory:sync-former-students'],
)]
class SyncFormerStudentsCommand extends Command
{
    public function __construct(
        protected UserRepository $userRepository,
        protected UserRoleRepository $userRoleRepository,
        protected Directory $directory
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'filter',
                InputArgument::REQUIRED,
                'An LDAP filter to use in finding former students in the directory.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Starting former student synchronization process.</info>');
        $filter = $input->getArgument('filter');

        $formerStudents = $this->directory->findByLdapFilter($filter);

        if (!$formerStudents) {
            $output->writeln("<error>{$filter} returned no results.</error>");
            return Command::SUCCESS;
        }
        $output->writeln('<info>Found ' . count($formerStudents) . ' former students in the directory.</info>');

        $formerStudentsCampusIds = array_map(fn(array $arr) => $arr['campusId'], $formerStudents);

        $notFormerStudents = $this->userRepository->findUsersWhoAreNotFormerStudents($formerStudentsCampusIds);
        $usersToUpdate = $notFormerStudents->filter(fn(UserInterface $user) => !$user->isUserSyncIgnore());
        if (!$usersToUpdate->count() > 0) {
            $output->writeln("<info>There are no students to update.</info>");
            return Command::SUCCESS;
        }
        $output->writeln(
            '<info>There are ' .
            $usersToUpdate->count() .
            ' students in Ilios who will be marked as a Former Student.</info>'
        );
        $rows = $usersToUpdate->map(fn(UserInterface $user) => [
            $user->getCampusId(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getEmail(),
        ])->toArray();
        $table = new Table($output);
        $table->setHeaders(['Campus ID', 'First', 'Last', 'Email'])->setRows($rows);
        $table->render();

        $helper = $this->getHelper('question');
        $output->writeln('');
        $question = new ConfirmationQuestion(
            '<question>Do you wish to mark these users as Former Students? </question>' . "\n",
            true
        );

        if ($helper->ask($input, $output, $question)) {
            /** @var UserRoleInterface $formerStudentRole */
            $formerStudentRole = $this->userRoleRepository->findOneBy(['title' => 'Former Student']);
            /** @var UserInterface $user */
            foreach ($usersToUpdate as $user) {
                $formerStudentRole->addUser($user);
                $user->addRole($formerStudentRole);
                $this->userRepository->update($user, false);
            }
            $this->userRoleRepository->update($formerStudentRole);

            $output->writeln('<info>Former students updated successfully!</info>');

            return Command::SUCCESS;
        } else {
            $output->writeln('<comment>Update canceled,</comment>');

            return Command::FAILURE;
        }
    }
}
