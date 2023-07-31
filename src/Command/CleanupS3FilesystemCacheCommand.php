<?php

declare(strict_types=1);

namespace App\Command;

use App\Classes\DiskSpace;
use App\Service\FilesystemFactory;
use App\Service\IliosFileSystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\StorageAttributes;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Cleanup the S3 local cache before it grows too large
 *
 * @package App\Command
 */
class CleanupS3FilesystemCacheCommand extends Command
{
    protected FilesystemOperator $filesystem;
    protected string $localCacheDirectory;

    public function __construct(FilesystemFactory $filesystemFactory, protected DiskSpace $diskSpace)
    {
        parent::__construct();
        $this->filesystem = $filesystemFactory->getS3LocalFilesystemCache();
        $this->localCacheDirectory = $filesystemFactory->getLocalS3CacheDirectory();
    }

    protected function configure(): void
    {
        $this
            ->setName('ilios:cleanup-s3-cache')
            ->setDescription('Remove stale files if the cache disk space is low');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Checking for available disk space.</info>');
        $percentageFree = $this->getFreeSpace();

        if ($percentageFree > 30) {
            $output->writeln("<info>{$percentageFree}% free space. Not cleaning up any files.</info>");
            return 0;
        }
        $output->writeln("<info>{$percentageFree}% free space. Will cleanup old files...</info>");
        $contents = $this->filesystem->listContents(IliosFileSystem::HASHED_LM_DIRECTORY, true);
        $deleteBefore = strtotime("-48 hours");
        $deletedFiles = 0;
        /** @var StorageAttributes $object */
        foreach ($contents as $object) {
            if ($object->type() === 'file' && $object->lastModified() < $deleteBefore) {
                $this->filesystem->delete($object->path());
                $deletedFiles++;
            }
        }

        $output->writeln("<info>{$deletedFiles} file(s) cleaned up!</info>");
        $percentageFree = $this->getFreeSpace();
        $output->writeln("<info>{$percentageFree}% free space now.</info>");

        return 0;
    }

    protected function getFreeSpace()
    {
        $freeSpace = $this->diskSpace->freeSpace($this->localCacheDirectory);
        $totalSpace = $this->diskSpace->totalSpace($this->localCacheDirectory);
        return round($freeSpace / $totalSpace * 100, 0);
    }
}
