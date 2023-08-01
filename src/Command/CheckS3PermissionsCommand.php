<?php

declare(strict_types=1);

namespace App\Command;

use App\Exception\IliosFilesystemException;
use App\Service\Config;
use App\Service\IliosFileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Checks the configured S3 filesystem for any permission issues
 *
 * Class CheckS3PermissionsCommand
 */
class CheckS3PermissionsCommand extends Command
{
    public function __construct(
        protected Config $config,
        protected IliosFileSystem $iliosFileSystem
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('ilios:check-s3-permissions')
            ->setDescription('Checks the configured S3 filesystem for any permission issues.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $s3Url = $this->config->get('storage_s3_url');

        if (!$s3Url) {
            $output->writeln("<comment>No configuration found for storage_s3_url. Nothing to check.</comment>");

            return Command::SUCCESS;
        }
        try {
            $output->writeln("<info>Connecting to the filesystem and checking permissions.</info>");
            $this->iliosFileSystem->testCRUD();
            $output->writeln("<info>All Systems Go!!</info>");

            return Command::SUCCESS;
        } catch (IliosFilesystemException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');

            return Command::FAILURE;
        }
    }
}
