<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Archive;
use App\Service\Config;
use App\Service\Fetch;
use DateTime;
use SimpleXMLElement;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use App\Service\Filesystem;
use Exception;
use SplFileObject;

use function filectime;
use function is_dir;

/**
 * Pull down asset archive from AWS and extract it so
 * assets can be served from the API host.
 *
 * Class UpdateFrontendCommand
 * @package App\Command
 */
#[AsCommand(
    name: 'ilios:update-frontend',
    description: 'Updates the frontend to the latest version.',
    aliases: ['ilios:maintenance:update-frontend'],
)]
class UpdateFrontendCommand extends Command implements CacheWarmerInterface
{
    private const UNPACKED_DIRECTORY = '/deploy-dist/';
    private const FRONTEND_FILES = '/var/frontend/';
    private const STAGING_CDN_ASSET_DOMAIN = 'https://frontend-archive-staging.iliosproject.org/';
    private const STAGING_ASSET_LIST = 'https://frontend-archive-staging.s3.us-west-2.amazonaws.com/';
    private const PRODUCTION_CDN_ASSET_DOMAIN = 'https://frontend-archive-production.iliosproject.org/';
    private const PRODUCTION_ASSET_LIST = 'https://frontend-archive-production.s3.us-west-2.amazonaws.com/';
    private const STAGING = 'stage';
    private const PRODUCTION = 'prod';
    protected string $productionTemporaryFileStore;
    protected string $stagingTemporaryFileStore;
    protected string $frontendAssetDirectory;
    protected ?OutputInterface $output;

    public function __construct(
        protected Fetch $fetch,
        protected Filesystem $fs,
        protected Config $config,
        private Archive $archive,
        protected string $kernelProjectDir,
        protected string $apiVersion,
        protected string $environment
    ) {
        $temporaryFileStorePath = $this->kernelProjectDir . self::FRONTEND_FILES . "/assets";
        $this->fs->mkdir($temporaryFileStorePath);
        $this->productionTemporaryFileStore = $temporaryFileStorePath . '/prod';
        $this->fs->mkdir($this->productionTemporaryFileStore);
        $this->stagingTemporaryFileStore = $temporaryFileStorePath . '/stage';
        $this->fs->mkdir($this->stagingTemporaryFileStore);
        $this->frontendAssetDirectory = "{$kernelProjectDir}/public/";
        $this->output = null;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'staging-build',
                null,
                InputOption::VALUE_NONE,
                'Pull a staging build of the frontend'
            )
            ->addOption(
                'at-version',
                null,
                InputOption::VALUE_REQUIRED,
                'Request a specific version of the frontend (instead of the default active one)'
            )
            ->addOption(
                'limit',
                null,
                InputOption::VALUE_REQUIRED,
                'Limit the number of past frontend versions to download. Set to zero for all versions.',
                3,
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stagingBuild = $input->getOption('staging-build');
        $versionOverride = $input->getOption('at-version');
        $limit = (int) $input->getOption('limit');
        $environment = $stagingBuild ? self::STAGING : self::PRODUCTION;
        $this->output = $output;
        $message = '';
        if ($stagingBuild) {
            $message .= ' from staging build';
        }
        if ($versionOverride) {
            $message .= ' at version ' . $versionOverride;
        }

        try {
            $currentVersion = $this->downloadAndExtractArchives($environment, $limit);
            $distributions = $this->listDistributions($environment);
            foreach ($distributions as $path) {
                $this->copyAssetsIntoPublicDirectory($path);
            }
            $versionToActivate = $versionOverride ?? $currentVersion;
            if ($versionToActivate) {
                $currentDistributionPathArr = array_filter(
                    $distributions,
                    fn(string $p) => str_ends_with($p, $versionToActivate)
                );
                if (empty($currentDistributionPathArr)) {
                    throw new Exception();
                }
                $currentDistributionPath = array_values($currentDistributionPathArr)[0];
                //re-copy current version for non fingerprinted assets to be placed last
                $this->copyAssetsIntoPublicDirectory($currentDistributionPath);
                $this->activateVersion($currentDistributionPath);
            }
            $output->writeln("<info>Frontend updated successfully{$message}!</info>");

            return Command::SUCCESS;
        } catch (Exception) {
            $output->writeln("<error>No matching frontend found{$message}!</error>");

            return Command::FAILURE;
        }
    }

    public function warmUp(string $cacheDir, ?string $buildDir = null): array
    {
        try {
            $currentVersion = $this->downloadAndExtractArchives(self::PRODUCTION, 3);
            $distributions = $this->listDistributions(self::PRODUCTION);
            foreach ($distributions as $path) {
                $this->copyAssetsIntoPublicDirectory($path);
            }
            $version = $currentVersion;
            $releaseVersion = $this->config->get('frontend_release_version');
            $keepFrontendUpdated = $this->config->get('keep_frontend_updated');
            if (!$keepFrontendUpdated) {
                $version = $releaseVersion;
            }

            if ($version) {
                $currentDistributionPathArr = array_filter(
                    $distributions,
                    fn(string $p) => str_ends_with($p, $version)
                );
                $currentDistributionPath = array_values($currentDistributionPathArr)[0];
                //re-copy current version for non fingerprinted assets to be placed last
                $this->copyAssetsIntoPublicDirectory($currentDistributionPath);
                $this->activateVersion($currentDistributionPath);
            }
        } catch (Exception) {
            print "\n\n**Warning: Unable to load frontend. Please run ilios:maintenance:update-frontend again.**\n\n\n";
        }

        return [];
    }

    public function isOptional(): bool
    {
        return true;
    }

    protected function activateVersion(string $distributionPath): void
    {
        $frontendPath = self::getActiveFrontendIndexPath($this->kernelProjectDir);
        $this->fs->remove($frontendPath);
        $this->fs->copy("{$distributionPath}/index.json", $frontendPath);
    }

    protected function downloadAndExtractArchives(string $environment, int $limit): ?string
    {
        $this->optionalOutput('Downloading List of Frontend Distributions...');
        $distributions = $this->extractS3Index($environment);
        if (empty($distributions)) {
            $this->optionalOutput('There are no current frontend distributions for your API version', 'comment');
            return null;
        }
        if ($limit) {
            $count = count($distributions);
            if ($limit < $count) {
                $this->optionalOutput("Found {$count}, using the {$limit} most recent.");
            }
            $distributions = array_slice($distributions, 0, $limit);
        }
        $this->optionalOutput('Done!');
        $progressBar = $this->output ? new ProgressBar($this->output, count($distributions)) : null;
        $progressBar?->setFormat('%message% %current%/%max% [%bar%] %percent:3s%%');
        $progressBar?->setMessage('Downloading Frontend Archives');
        $progressBar?->start();
        foreach ($distributions as $distribution) {
            $this->downloadAndExtractArchive(
                $distribution['key'],
                $distribution['url'],
                $distribution['lastModified'],
                $environment === 'prod' ? $this->productionTemporaryFileStore : $this->stagingTemporaryFileStore
            );
            $progressBar?->advance();
        }
        $progressBar?->finish();
        $this->optionalOutput('');
        $this->optionalOutput('Done!');

        $currentVersion = array_filter($distributions, fn(array $arr) => $arr['isCurrentVersion']);
        if (count($currentVersion)) {
            $key = array_values($currentVersion)[0]['key'];
            return explode(':', $key)[1];
        }
        return null;
    }

    protected function extractS3Index(string $environment): array
    {
        $url = self::PRODUCTION_ASSET_LIST;
        if ($environment === self::STAGING) {
            $url = self::STAGING_ASSET_LIST;
        }

        $assetUrl = self::PRODUCTION_CDN_ASSET_DOMAIN;
        if ($environment === self::STAGING) {
            $assetUrl = self::STAGING_CDN_ASSET_DOMAIN;
        }
        $xmlString = $this->fetch->get($url . '?prefix=' . $this->apiVersion);
        $xml = new SimpleXMLElement($xmlString);
        $currentVersionEtag = false;
        foreach ($xml->Contents as $element) {
            if (str_ends_with((string) $element->Key, 'frontend.tar.gz')) {
                $currentVersionEtag = (string) $element->ETag;
            }
        }
        $allDistributions = [];
        if ($xml->Contents) {
            foreach ($xml->Contents as $element) {
                $etag = (string) $element->ETag;
                $allDistributions[] = [
                    'key' => (string) $element->Key,
                    'url' => $assetUrl . (string) $element->Key,
                    'lastModified' => new DateTime((string) $element->LastModified),
                    'eTag' => trim($etag, '"'),
                    'isCurrentVersion' => $etag === $currentVersionEtag,
                ];
            }
        }
        $rhett =  array_filter($allDistributions, fn(array $arr) => !str_ends_with($arr['key'], 'frontend.tar.gz'));

        //reverse sort so we get the latest release first
        usort($rhett, fn(array $a, array $b) => $b['lastModified'] <=> $a['lastModified']);

        return $rhett;
    }

    protected function downloadAndExtractArchive(
        string $key,
        string $url,
        DateTime $lastModified,
        string $archiveDir
    ): void {
        $parts = [
            $archiveDir,
            $key,
        ];
        $archivePath = join(DIRECTORY_SEPARATOR, $parts);
        $version = explode(':', $key)[1];
        $file = is_readable($archivePath) ? new SplFileObject($archivePath, "r") : null;
        $downloadedLastModified = $file ? new DateTime('@' . $file->getMTime()) : false;
        if (!$downloadedLastModified || $downloadedLastModified < $lastModified) {
            $string = $this->fetch->get($url, $file);
            $this->fs->dumpFile($archivePath, $string);
        }
        $destination = dirname($archivePath) . DIRECTORY_SEPARATOR . $version;
        if ($this->fs->exists($destination)) {
            //when we get a new version of the same archive (like for PR previews) we have to remove the old directory
            $destinationCreatedAt = DateTime::createFromFormat('U', (string) filectime($destination));
            if ($destinationCreatedAt < $lastModified) {
                $this->fs->remove([$destination]);
            }
        }
        if (!$this->fs->exists($destination)) {
            $tmp = $destination . '-tmp';
            $this->archive::extract($archivePath, $tmp);
            $this->fs->rename($tmp . self::UNPACKED_DIRECTORY, $destination);
            $this->fs->remove($tmp);
        }
    }

    protected function listDistributions(string $environment): array
    {
        $envDir = $environment === 'prod' ? $this->productionTemporaryFileStore : $this->stagingTemporaryFileStore;
        $archiveDir = $envDir . DIRECTORY_SEPARATOR . $this->apiVersion;
        $files = array_diff($this->fs->scandir($archiveDir), ['.', '..']);
        $paths = array_map(fn(string $fileName) => $archiveDir . DIRECTORY_SEPARATOR . $fileName, $files);
        $arr = array_filter(
            $paths,
            fn(string $path) => is_dir($path)
        );
        return array_values($arr);
    }

    protected function copyAssetsIntoPublicDirectory(string $distributionPath): void
    {
        $filesToIgnore = ['..', '.', 'index.json', 'index.html', '_redirects'];
        $files = array_diff($this->fs->scandir($distributionPath), $filesToIgnore);
        foreach ($files as $file) {
            $path = $distributionPath . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) {
                $this->fs->mirror($path, $this->frontendAssetDirectory . $file);
            } else {
                $this->fs->copy($path, $this->frontendAssetDirectory . $file, true);
            }
        }
    }

    protected function optionalOutput(string $message, string $type = 'info'): void
    {
        if ($this->output) {
            $this->output->writeln("<{$type}>{$message}</{$type}>");
        }
    }

    public static function getActiveFrontendIndexPath(string $kernelProjectDir): string
    {
        return $kernelProjectDir . self::FRONTEND_FILES  . '/index.json';
    }
}
