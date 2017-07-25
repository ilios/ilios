<?php

namespace Ilios\CliBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

use Ilios\CoreBundle\Service\Directory;

/**
 * Find a user in the directory
 *
 * Class FindUserCommand
 */
class FindUserCommand extends Command
{
    /**
     * @var Directory
     */
    protected $directory;
    
    public function __construct(
        Directory $directory
    ) {
        $this->directory = $directory;
        
        parent::__construct();
    }
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:directory:find-user')
            ->setDescription('Find a user in the directory.')
            ->addArgument(
                'searchTerms',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'What to search for (separate multiple names with a space).'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $searchTerms = $input->getArgument('searchTerms');
        $userRecords = $this->directory->find($searchTerms);
        if (!$userRecords) {
            $output->writeln('<error>Unable to find anyone matching those terms in the directory.</error>');
            return;
        }
        
        $rows = array_map(function ($arr) {
            return [
                $arr['campusId'],
                $arr['firstName'],
                $arr['lastName'],
                $arr['email'],
                $arr['telephoneNumber']
            ];
        }, $userRecords);
        $table = new Table($output);
        $table
            ->setHeaders(array('Campus ID', 'First', 'Last', 'Email', 'Phone Number'))
            ->setRows($rows)
        ;
        $table->render();
    }
}
