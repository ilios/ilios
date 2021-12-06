<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\UserRoleInterface;
use App\Repository\UserRepository;
use App\Repository\UserRoleRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use App\Entity\UserInterface;
use App\Service\Directory;

/**
 * Syncs students from the directory.
 */
class SyncStudentStatusCommand extends Command
{
    public function __construct(
        protected UserRepository $userRepository,
        protected UserRoleRepository $userRoleRepository,
        protected Directory $directory
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('ilios:sync-students')
            ->setDescription('Sync students from the directory.')
            ->addArgument(
                'filter',
                InputArgument::REQUIRED,
                'An LDAP filter to use in finding students in the directory.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Starting student synchronization process.</info>');
        $filter = $input->getArgument('filter');

        $students = $this->directory->findByLdapFilter($filter);

        if (!$students) {
            $output->writeln("<error>{$filter} returned no results.</error>");
            return 0;
        }
        $output->writeln('<info>Found ' . count($students) . ' students in the directory.</info>');

        $studentsCampusIds = array_map(fn(array $arr) => $arr['campusId'], $students);

        $notStudents = $this->userRepository->findUsersWhoAreNotStudents($studentsCampusIds);
        $usersToUpdate = array_filter(
            $notStudents,
            fn(UserInterface $user) => !$user->isUserSyncIgnore() && $user->isEnabled()
        );
        if ($usersToUpdate === []) {
            $output->writeln("<info>There are no students to update.</info>");
            return 0;
        }
        $output->writeln(
            '<info>There are ' .
            count($usersToUpdate) .
            ' students in Ilios who will be marked as a Student.</info>'
        );
        $rows = array_map(fn(UserInterface $user) => [
            $user->getCampusId(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getEmail(),
        ], $usersToUpdate);
        $table = new Table($output);
        $table->setHeaders(['Campus ID', 'First', 'Last', 'Email'])->setRows($rows);
        $table->render();

        $helper = $this->getHelper('question');
        $output->writeln('');
        $question = new ConfirmationQuestion(
            '<question>Do you wish to mark these users as Students? </question>' . "\n",
            true
        );

        if ($helper->ask($input, $output, $question)) {
            $studentRole = $this->userRoleRepository->findOneBy(['title' => 'Student']);
            foreach ($usersToUpdate as $user) {
                $user->addRole($studentRole);
                $this->userRepository->update($user, false);
            }
            $this->userRepository->flush();

            $output->writeln('<info>Students updated successfully!</info>');

            return 0;
        } else {
            $output->writeln('<comment>Update canceled,</comment>');

            return 1;
        }
    }
}
