<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\ApplicationConfig;
use App\Repository\ApplicationConfigRepository;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Set an application configuration value in the DB
 *
 * Class SetConfigValueCommand
 * @package App\Command
 */
class SetConfigValueCommand extends Command
{
    /**
     * SetConfigValueCommand constructor.
     */
    public function __construct(protected ApplicationConfigRepository $applicationConfigRepository)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('ilios:set-config-value')
            ->setAliases(['ilios:maintenance:set-config-value'])
            ->setDescription('Set a configuration value in the DB')
            //required arguments
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'The name of the configuration we are setting'
            )
            ->addOption(
                'remove',
                'r',
                InputOption::VALUE_NONE,
                'Remove the value instead of setting it'
            )
            ->addArgument(
                'value',
                InputArgument::OPTIONAL,
                'The value of the configuration we are setting'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $value = $input->getArgument('value');
        $isRemoving = $input->getOption('remove');
        if (!$isRemoving && !$value) {
            throw new RuntimeException("'value' is required");
        }

        /** @var ApplicationConfig $config */
        $config = $this->applicationConfigRepository->findOneBy(['name' => $name]);

        if ($isRemoving) {
            if ($config) {
                $this->applicationConfigRepository->delete($config);
                $output->writeln("${name} removed.");
                return Command::SUCCESS;
            } else {
                $output->writeln("<error>There was no value in the databse for ${name}</error>");
                return Command::FAILURE;
            }
        }

        if (!$config) {
            $config = $this->applicationConfigRepository->create();
            $config->setName($name);
        }
        $config->setValue($value);
        $this->applicationConfigRepository->update($config, true);

        $output->writeln('<info>Done.</info>');

        return Command::SUCCESS;
    }
}
