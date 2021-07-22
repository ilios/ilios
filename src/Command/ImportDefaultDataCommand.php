<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\AamcMethodRepository;
use App\Repository\AamcPcrsRepository;
use App\Repository\AamcResourceTypeRepository;
use App\Repository\AlertChangeTypeRepository;
use App\Repository\ApplicationConfigRepository;
use App\Repository\AssessmentOptionRepository;
use App\Repository\CompetencyRepository;
use App\Repository\SchoolRepository;
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
        protected AamcMethodRepository $aamcMethodRepository,
        protected AamcPcrsRepository $aamcPcrsRepository,
        protected AamcResourceTypeRepository $aamcResourceTypeRepository,
        protected AlertChangeTypeRepository $alertChangeTypeRepository,
        protected ApplicationConfigRepository $applicationConfigRepository,
        protected AssessmentOptionRepository $assessmentOptionRepository,
        protected SchoolRepository $schoolRepository,
        protected CompetencyRepository $competencyRepository
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
            // clear data
            $this->aamcMethodRepository->clearData();
            $this->aamcPcrsRepository->clearData();
            $this->aamcResourceTypeRepository->clearData();
            $this->alertChangeTypeRepository->clearData();
            $this->applicationConfigRepository->clearData();
            $this->assessmentOptionRepository->clearData();
            $this->competencyRepository->clearData();
            $this->schoolRepository->clearData();

            // import data
            $this->dataLoader->import($this->aamcMethodRepository, 'aamc_method.csv');
            $this->dataLoader->import($this->aamcPcrsRepository, 'aamc_pcrs.csv');
            $this->dataLoader->import($this->aamcResourceTypeRepository, 'aamc_resource_type.csv');
            $this->dataLoader->import($this->alertChangeTypeRepository, 'alert_change_type.csv');
            $this->dataLoader->import($this->applicationConfigRepository, 'application_config.csv');
            $this->dataLoader->import($this->assessmentOptionRepository, 'assessment_option.csv');
            $this->dataLoader->import($this->schoolRepository, 'school.csv');
            $this->dataLoader->import($this->competencyRepository, 'competency.csv', 'competency');
            $this->dataLoader->import(
                $this->competencyRepository,
                'competency_x_aamc_pcrs.csv',
                'competency_x_aamc_pcrs'
            );
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
