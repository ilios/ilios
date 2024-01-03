<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\SchoolConfigInterface;
use App\Entity\SchoolInterface;
use App\Repository\SchoolConfigRepository;
use App\Repository\SchoolRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Get a school configuration value from the DB
 */
#[AsCommand(
    name: 'ilios:list-school-config-values',
    description: 'Read school configuration values from the DB',
    aliases: ['ilios:maintenance:list-school-config-values']
)]
class ListSchoolConfigValuesCommand extends Command
{
    public function __construct(
        protected SchoolRepository $schoolRepository,
        protected SchoolConfigRepository $schoolConfigRepository
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('school', InputArgument::REQUIRED, 'ID of the school.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $schoolId = $input->getArgument('school');
        $school = $this->schoolRepository->findOneBy(['id' => $schoolId]);
        if (!$school) {
            $output->writeln("<error>There are no schools with id {$schoolId}.</error>");
            return Command::FAILURE;
        }
        /** @var SchoolConfigInterface[] $configs */
        $configs = $this->schoolConfigRepository->findBy(['school' => $schoolId], ['name' => 'asc']);
        if (empty($configs)) {
            $output->writeln('<error>There are no configuration values in the database.</error>');
        } else {
            $table = new Table($output);
            $table->setHeaders(['Name', 'Value'])->setRows(
                array_map(fn(SchoolConfigInterface $config) => [$config->getName(), $config->getValue()], $configs)
            );
            $table->render();
        }

        return Command::SUCCESS;
    }
}
