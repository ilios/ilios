<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use App\Repository\AuthenticationRepository;
use App\Repository\PendingUserUpdateRepository;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use App\Service\Directory;

/**
 * Sync a user with their directory information
 *
 * Class SyncUserCommand
 */
class SyncUserCommand extends Command
{
    public function __construct(
        protected UserRepository $userRepository,
        protected AuthenticationRepository $authenticationRepository,
        protected PendingUserUpdateRepository $pendingUserUpdateRepository,
        protected Directory $directory
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:sync-user')
            ->setAliases(['ilios:directory:sync-user'])
            ->setDescription('Sync a user from the directory.')
            ->addArgument(
                'userId',
                InputArgument::REQUIRED,
                'A valid user id.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userId = $input->getArgument('userId');
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['id' => $userId]);
        if (!$user) {
            throw new \Exception(
                "No user with id #{$userId} was found."
            );
        }

        $userRecord = $this->directory->findByCampusId($user->getCampusId());

        if (!$userRecord) {
            $output->writeln('<error>Unable to find ' . $user->getCampusId() . ' in the directory.');
            return 1;
        }

        $table = new Table($output);
        $table
            ->setHeaders([
                'Record',
                'Campus ID',
                'First',
                'Last',
                'Display Name',
                'Email',
                'Phone Number'
            ])
            ->setRows([
                [
                    'Ilios User',
                    $user->getCampusId(),
                    $user->getFirstName(),
                    $user->getLastName(),
                    $user->getDisplayName(),
                    $user->getEmail(),
                    $user->getPhone()
                ],
                [
                    'Directory User',
                    $userRecord['campusId'],
                    $userRecord['firstName'],
                    $userRecord['lastName'],
                    $userRecord['displayName'],
                    $userRecord['email'],
                    $userRecord['telephoneNumber']
                ]
            ])
        ;
        $table->render();

        $helper = $this->getHelper('question');
        $output->writeln('');
        $question = new ConfirmationQuestion(
            '<question>Do you wish to update this Ilios User with the data ' .
            'from the Directory User? </question>' . "\n",
            true
        );

        if ($helper->ask($input, $output, $question)) {
            $user->setFirstName($userRecord['firstName']);
            $user->setLastName($userRecord['lastName']);
            $user->setDisplayName($userRecord['displayName']);
            $user->setEmail($userRecord['email']);
            $user->setPhone($userRecord['telephoneNumber']);
            $authentication = $user->getAuthentication();
            if (!$authentication) {
                $authentication = $this->authenticationRepository->create();
                $authentication->setUser($user);
            }

            $authentication->setUsername($userRecord['username']);
            $this->authenticationRepository->update($authentication, false);

            $this->userRepository->update($user);

            foreach ($user->getPendingUserUpdates() as $update) {
                $this->pendingUserUpdateRepository->delete($update);
            }

            $output->writeln('<info>User updated successfully!</info>');

            return 0;
        } else {
            $output->writeln('<comment>Update canceled.</comment>');

            return 1;
        }
    }
}
