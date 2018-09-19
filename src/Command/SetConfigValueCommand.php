<?php

namespace App\Command;

use App\Entity\ApplicationConfig;
use App\Entity\Manager\ApplicationConfigManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Set an application configuration value in the DB
 *
 * Class SetConfigValueCommand
 * @package AppBundle\Command
 */
class SetConfigValueCommand extends Command
{
    /**
     * @var ApplicationConfigManager
     */
    protected $applicationConfigManager;

    /**
     * SetConfigValueCommand constructor.
     * @param ApplicationConfigManager $applicationConfigManager
     */
    public function __construct(ApplicationConfigManager $applicationConfigManager)
    {
        $this->applicationConfigManager = $applicationConfigManager;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('ilios:maintenance:set-config-value')
            ->setDescription('Set a configuration value in the DB')
            //required arguments
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'The name of the configuration we are setting'
            )
            ->addArgument(
                'value',
                InputArgument::REQUIRED,
                'The value of the configuration we are setting'
            );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $value = $input->getArgument('value');

        /** @var ApplicationConfig $config */
        $config = $this->applicationConfigManager->findOneBy(['name' => $name]);
        if (!$config) {
            $config = $this->applicationConfigManager->create();
            $config->setName($name);
        }
        $config->setValue($value);

        $this->applicationConfigManager->update($config, true);

        $output->writeln('<info>Done.</info>');
    }
}
