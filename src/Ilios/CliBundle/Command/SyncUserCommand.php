<?php

namespace Ilios\CliBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\ConfirmationQuestion;

use Ilios\CoreBundle\Entity\Manager\UserManager;
use Ilios\CoreBundle\Entity\Manager\AuthenticationManager;
use Ilios\CoreBundle\Service\Directory;

/**
 * Sync a user with their directory information
 *
 * Class SyncUserCommand
 */
class SyncUserCommand extends Command
{
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var AuthenticationManager
     */
    protected $authenticationManager;
    
    /**
     * @var Directory
     */
    protected $directory;
    
    public function __construct(
        UserManager $userManager,
        AuthenticationManager $authenticationManager,
        Directory $directory
    ) {
        $this->userManager = $userManager;
        $this->authenticationManager = $authenticationManager;
        $this->directory = $directory;
        
        parent::__construct();
    }
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:directory:sync-user')
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
        $user = $this->userManager->findOneBy(['id' => $userId]);
        if (!$user) {
            throw new \Exception(
                "No user with id #{$userId} was found."
            );
        }
        
        $userRecord = $this->directory->findByCampusId($user->getCampusId());
        
        if (!$userRecord) {
            $output->writeln('<error>Unable to find ' . $user->getCampusId() . ' in the directory.');
            return;
        }
        
        $table = new Table($output);
        $table
            ->setHeaders(array('Record', 'Campus ID', 'First', 'Last', 'Email', 'Phone Number'))
            ->setRows(array(
                [
                    'Ilios User',
                    $user->getCampusId(),
                    $user->getFirstName(),
                    $user->getLastName(),
                    $user->getEmail(),
                    $user->getPhone()
                ],
                [
                    'Directory User',
                    $userRecord['campusId'],
                    $userRecord['firstName'],
                    $userRecord['lastName'],
                    $userRecord['email'],
                    $userRecord['telephoneNumber']
                ]
            ))
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
            $user->setEmail($userRecord['email']);
            $user->setPhone($userRecord['telephoneNumber']);
            $authentication = $user->getAuthentication();
            if (!$authentication) {
                $authentication = $this->authenticationManager->create();
                $authentication->setUser($user);
            }

            $authentication->setUsername($userRecord['username']);
            $this->authenticationManager->update($authentication, false);
            
            $this->userManager->update($user);
            
            $output->writeln('<info>User updated successfully!</info>');
        } else {
            $output->writeln('<comment>Update canceled.</comment>');
        }
    }
}
