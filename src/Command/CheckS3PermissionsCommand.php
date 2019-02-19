<?php

namespace App\Command;

use App\Exception\IliosFilesystemException;
use App\Service\Config;
use App\Service\IliosFileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Checks the configured S3 filesystem for any permission issues
 *
 * Class CheckS3PermissionsCommand
 */
class CheckS3PermissionsCommand extends Command
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var IliosFileSystem
     */
    protected $iliosFileSystem;

    public function __construct(
        Config $config,
        IliosFileSystem $iliosFileSystem
    ) {
        parent::__construct();
        $this->config = $config;
        $this->iliosFileSystem = $iliosFileSystem;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:check-s3-permissions')
            ->setDescription('Checks the configured S3 filesystem for any permission issues.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $s3Url = $this->config->get('storage_s3_url');

        if (!$s3Url) {
            $output->writeln("<comment>No configuration found for storage_s3_url. Nothing to check.</comment>");
            exit();
        }
        try {
            $output->writeln("<info>Connecting to the filesystem and checking permissions.</info>");
            $this->iliosFileSystem->testCRUD();
            $output->writeln("<info>All Systems Go!!</info>");
        } catch (IliosFilesystemException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            exit(1);
        }
    }
}
