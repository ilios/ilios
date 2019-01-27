<?php

namespace App\Command;

use App\Entity\DTO\UserDTO;
use App\Entity\User;
use App\Service\Search;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Question\ConfirmationQuestion;

use App\Entity\Manager\UserManager;
use App\Entity\Manager\AuthenticationManager;

/**
 * Populates the search index with documents
 *
 * Class PopulateIndexCommand
 */
class PopulateIndexCommand extends Command
{
    /**
     * @var Search
     */
    protected $search;

    /**
     * @var UserManager
     */
    protected $userManager;

    
    public function __construct(
        Search $search,
        UserManager $userManager
    ) {
        parent::__construct();

        $this->search = $search;
        $this->userManager = $userManager;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:populate-index')
            ->setDescription('Populate the search index with documents.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Clearing the index and preparing to insert data.</info>");
        $this->search->clear();
        $output->writeln("<info>Ok.</info>");

        $output->writeln("<info>Clearing the index and preparing to insert data.</info>");
        $output->writeln("<info>Adding Users...</info>");
        $allIds = $this->userManager->getAllIds();
        $progressBar = new ProgressBar($output, count($allIds));
        $progressBar->start();
        $chunks = array_chunk($allIds, 500);
        foreach ($chunks as $ids) {
            $dtos = $this->userManager->findDTOsBy(['id' => $ids]);
            $users = array_map(function (UserDTO $user) {
                return [
                    'id' => $user->id,
                    'firstName' => $user->firstName,
                    'lastName' => $user->lastName,
                    'middleName' => $user->middleName,
                    'email' => $user->email,
                    'campusId' => $user->campusId,
                    'username' => $user->username,
                ];
            }, $dtos);

            $this->search->bulkIndex(Search::PRIVATE_INDEX, User::class, $users);
            $progressBar->advance(count($ids));
        }
        $progressBar->finish();
        $output->writeln("<info>done!</info>");
    }
}
