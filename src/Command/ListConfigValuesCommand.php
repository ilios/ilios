<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\ApplicationConfig;
use App\Repository\ApplicationConfigRepository;
use Doctrine\DBAL\Exception\ConnectionException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Get an application configuration value from the DB
 *
 * Class ListConfigValuesCommand
 * @package App\Command
 */
#[AsCommand(
    name: 'ilios:list-config-values',
    description: 'Read configuration values from the DB',
    aliases: ['ilios:maintenance:list-config-values']
)]
class ListConfigValuesCommand extends Command
{
    /**
     * RolloverCourseCommand constructor.
     */
    public function __construct(
        protected ApplicationConfigRepository $applicationConfigRepository,
        protected string $environment,
        protected string $kernelSecret,
        protected string $databaseUrl
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            /** @var ApplicationConfig[] $configs */
            $configs = $this->applicationConfigRepository->findBy([], ['name' => 'asc']);
        } catch (ConnectionException $e) {
            $output->writeln('<error>Unable to connect to database.</error>');
            $output->writeln("<error>{$e->getMessage()}</error>");
        }
        if (empty($configs)) {
            $output->writeln('<error>There are no configuration values in the database.</error>');
        } else {
            $table = new Table($output);
            $table->setHeaderTitle('Database Values');
            $table->setHeaders(['Name', 'Value'])->setRows(
                array_map(fn(ApplicationConfig $config) => [$config->getName(), $config->getValue()], $configs)
            );
            $table->render();
        }

        $rows = [
          ['Environment', $this->environment],
          ['Kernel Secret', $this->kernelSecret],
          ['Database URL', $this->databaseUrl],
        ];
        foreach ($_ENV as $key => $value) {
            if (str_starts_with($key, "ILIOS_")) {
                $rows[] = [$key, $value];
            }
        }
        $table = new Table($output);
        $table->setHeaderTitle('Environment Values');
        $table->setHeaders(['Name', 'Value']);
        $table->setRows($rows);
        $table->render();

        return Command::SUCCESS;
    }
}
