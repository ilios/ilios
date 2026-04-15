<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\ApplicationConfigRepository;
use RuntimeException;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Set an application configuration value in the DB
 *
 * Class SetConfigValueCommand
 * @package App\Command
 */
#[AsCommand(
    name: 'ilios:set-config-value',
    description: 'Set a configuration value in the DB',
    aliases: ['ilios:maintenance:set-config-value'],
)]
class SetConfigValueCommand extends Command
{
    /**
     * SetConfigValueCommand constructor.
     */
    public function __construct(protected ApplicationConfigRepository $applicationConfigRepository)
    {
        parent::__construct();
    }

    public function __invoke(
        OutputInterface $output,
        #[Argument(description: 'The name of the configuration we are setting')] string $name,
        #[Argument(description: 'The value of the configuration we are setting')] ?string $value,
        #[Option(description: 'Remove the value instead of setting it', shortcut: 'r')] bool $remove = false,
    ): int {
        if (!$remove && !$value) {
            throw new RuntimeException("'value' is required");
        }

        $config = $this->applicationConfigRepository->findOneBy(['name' => $name]);

        if ($remove) {
            if ($config) {
                $this->applicationConfigRepository->delete($config);
                $output->writeln("{$name} removed.");
                return Command::SUCCESS;
            } else {
                $output->writeln("<error>There was no value in the databse for {$name}</error>");
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
