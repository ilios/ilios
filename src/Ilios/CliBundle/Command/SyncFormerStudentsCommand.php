<?php

namespace Ilios\CliBundle\Command;

use Ilios\CoreBundle\Entity\UserRoleInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\ConfirmationQuestion;

use Ilios\CoreBundle\Entity\Manager\UserRoleManager;
use Ilios\CoreBundle\Entity\Manager\UserManager;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Service\Directory;

/**
 * Syncs former students from the directory.
 *
 * Class SyncFormerStudentsCommand
 */
class SyncFormerStudentsCommand extends Command
{
    /**
     * @var UserManager
     */
    protected $userManager;
    
    /**
     * @var UserRoleManager
     */
    protected $userRoleManager;
    
    /**
     * @var Directory
     */
    protected $directory;
    
    public function __construct(
        UserManager $userManager,
        UserRoleManager $userRoleManager,
        Directory $directory
    ) {
        $this->userManager = $userManager;
        $this->userRoleManager = $userRoleManager;
        $this->directory = $directory;
        
        parent::__construct();
    }
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:directory:sync-former-students')
            ->setDescription('Sync former students from the directory.')
            ->addArgument(
                'filter',
                InputArgument::REQUIRED,
                'An LDAP filter to use in finding former students in the directory.'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Starting former student synchronization process.</info>');
        $filter = $input->getArgument('filter');
        
        $formerStudents = $this->directory->findByLdapFilter($filter);
        
        if (!$formerStudents) {
            $output->writeln("<error>{$filter} returned no results.</error>");
            return;
        }
        $output->writeln('<info>Found ' . count($formerStudents) . ' former students in the directory.</info>');
        
        $formerStudentsCampusIds = array_map(function (array $arr) {
            return $arr['campusId'];
        }, $formerStudents);

        $notFormerStudents = $this->userManager->findUsersWhoAreNotFormerStudents($formerStudentsCampusIds);
        $usersToUpdate = $notFormerStudents->filter(function (UserInterface $user) {
            return !$user->isUserSyncIgnore();
        });
        if (!$usersToUpdate->count() > 0) {
            $output->writeln("<info>There are no students to update.</info>");
            return;
        }
        $output->writeln(
            '<info>There are ' .
            $usersToUpdate->count() .
            ' students in Ilios who will be marked as a Former Student.</info>'
        );
        $rows = $usersToUpdate->map(function (UserInterface $user) {
            return [
                $user->getCampusId(),
                $user->getFirstName(),
                $user->getLastName(),
                $user->getEmail(),
            ];
        })->toArray();
        $table = new Table($output);
        $table->setHeaders(array('Campus ID', 'First', 'Last', 'Email'))->setRows($rows);
        $table->render();

        $helper = $this->getHelper('question');
        $output->writeln('');
        $question = new ConfirmationQuestion(
            '<question>Do you wish to mark these users as Former Students? </question>' . "\n",
            true
        );
        
        if ($helper->ask($input, $output, $question)) {
            /* @var UserRoleInterface $formerStudentRole */
            $formerStudentRole = $this->userRoleManager->findOneBy(array('title' => 'Former Student'));
            /* @var UserInterface $user */
            foreach ($usersToUpdate as $user) {
                $formerStudentRole->addUser($user);
                $user->addRole($formerStudentRole);
                $this->userManager->update($user, false);
            }
            $this->userRoleManager->update($formerStudentRole);
            
            $output->writeln('<info>Former students updated successfully!</info>');
        } else {
            $output->writeln('<comment>Update canceled,</comment>');
        }
    }
}
