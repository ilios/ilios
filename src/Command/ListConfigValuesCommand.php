<?php

namespace App\Command;

use App\Entity\ApplicationConfig;
use App\Entity\Manager\ApplicationConfigManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Get an application configuration value from the DB
 *
 * Class ListConfigValuesCommand
 * @package App\Command
 */
class ListConfigValuesCommand extends Command
{
    /**
     * @var ApplicationConfigManager
     */
    protected $applicationConfigManager;
    protected $kernelSecret;
    protected $databaseUrl;
    protected $environment;

    /**
     * RolloverCourseCommand constructor.
     * @param ApplicationConfigManager $applicationConfigManager
     * @param $environment
     * @param $kernelSecret
     * @param $databaseUrl
     */
    public function __construct(
        ApplicationConfigManager $applicationConfigManager,
        $environment,
        $kernelSecret,
        $databaseUrl
    ) {
        $this->applicationConfigManager = $applicationConfigManager;
        $this->environment = $environment;
        $this->kernelSecret = $kernelSecret;
        $this->databaseUrl = $databaseUrl;

        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('ilios:list-config-values')
            ->setAliases(['ilios:maintenance:list-config-values'])
            ->setDescription('Read configuration values from the DB');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ApplicationConfig[] $configs */
        $configs = $this->applicationConfigManager->findBy([], ['name' => 'asc']);
        if (empty($configs)) {
            $output->writeln('<error>There are no configuration values in the database.</error>');
        } else {
            $table = new Table($output);
            $table->setHeaderTitle('Database Values');
            $table->setHeaders(array('Name', 'Value'))
                ->setRows(array_map(function (ApplicationConfig $config) {
                    return [$config->getName(), $config->getValue()];
                }, $configs));
            $table->render();
        }

        $rows = [
          ['Environment', $this->environment],
          ['Kernel Secret', $this->kernelSecret],
          ['Database URL', $this->databaseUrl],
        ];
        $table = new Table($output);
        $table->setHeaderTitle('Environment Values');
        $table->setHeaders(array('Name', 'Value'));
        $table->setRows($rows);
        $table->render();
    }
}
