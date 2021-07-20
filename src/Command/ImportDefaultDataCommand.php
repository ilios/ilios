<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\AamcMethodRepository;
use App\Service\DefaultDataLoader;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class ImportDefaultDataCommand extends Command
{
    use LockableTrait;

    public function __construct(
        protected DefaultDataLoader $dataLoader,
        protected AamcMethodRepository $aamcMethodRepository
    ) {
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $this
            ->setName('ilios:import-default-data')
            ->setAliases(['ilios:setup:import-default-data'])
            ->setDescription('Imports default application data into Ilios. Only works with an empty database schema.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');
            return 0;
        }

        $output->writeln(
            "<comment>Importing default data may wipe out any pre-existing records from your database.</comment>"
        );
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Continue?', false);

        if (!$helper->ask($input, $output, $question)) {
            return Command::SUCCESS;
        }

        $output->writeln('Started data import, this may take a while...');
        try {
            $this->dataLoader->import($this->aamcMethodRepository, 'aamc_method.csv');
        } catch (Exception $e) {
            $output->writeln("<error>An error occurred during data import:</error>");
            $output->write("<error>{$e->getMessage()}</error>");
            return Command::FAILURE;
        }
        $output->write('Completed data import.');
        $this->release();

        return Command::SUCCESS;
    }
}
