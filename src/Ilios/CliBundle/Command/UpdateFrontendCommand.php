<?php

namespace Ilios\CliBundle\Command;

use Ilios\CoreBundle\Service\ApplicationConfiguration;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

use Ilios\WebBundle\Service\WebIndexFromJson;
use Ilios\CoreBundle\Service\Filesystem;

/**
 * Build the index file from a published frontend release
 *
 * Class UpdateFrontendCommand
 */
class UpdateFrontendCommand extends Command implements CacheWarmerInterface
{
    /**
     * @var string
     */
    const CACHE_FILE_NAME = 'ilios/index.html';

    /**
     * @var WebIndexFromJson
     */
    protected $builder;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * @var string
     */
    protected $releaseVersion;

    /**
     * @var boolean
     */
    protected $keepFrontendUpdated;

    /**
     * @var string
     */
    protected $environment;
    
    public function __construct(
        WebIndexFromJson $builder,
        Filesystem $fs,
        ApplicationConfiguration $applicationConfiguration,
        $kernelCacheDir,
        $environment
    ) {
        $this->builder = $builder;
        $this->fs = $fs;
        $this->cacheDir = $kernelCacheDir;
        $this->releaseVersion = $applicationConfiguration->get('frontend_release_version');
        $this->keepFrontendUpdated = $applicationConfiguration->get('keep_frontend_updated');
        $this->environment = $environment;

        parent::__construct();
    }
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:maintenance:update-frontend')
            ->setDescription('Updates the frontend to the latest version.')
            ->addOption(
                'staging-build',
                null,
                InputOption::VALUE_NONE,
                'Pull a staging build of the frontend'
            )
            ->addOption(
                'dev-build',
                null,
                InputOption::VALUE_NONE,
                'Pull a dev build of the frontend'
            )
            ->addOption(
                'at-version',
                null,
                InputOption::VALUE_REQUIRED,
                'Request a specific version of the frontend (instead of the default active one)'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stagingBuild = $input->getOption('staging-build');
        $devBuild = $input->getOption('dev-build');
        $versionOverride = $input->getOption('at-version');

        $environment = WebIndexFromJson::PRODUCTION;
        if ($stagingBuild) {
            $environment = WebIndexFromJson::STAGING;
        }
        if ($devBuild) {
            $environment = WebIndexFromJson::DEVELOPMENT;
        }
        $version = $versionOverride?$versionOverride:null;
        $this->writeIndexFile($this->cacheDir, $environment, $version);

        $message = 'Frontend updated successfully';
        if ($stagingBuild) {
            $message .= ' from staging build';
        }
        if ($devBuild) {
            $message .= ' from dev build';
        }
        if ($versionOverride) {
            $message .= ' to version ' . $versionOverride;
        }
        $output->writeln("<info>{$message}!</info>");
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir)
    {
        try {
            $this->writeIndexFile($cacheDir, WebIndexFromJson::PRODUCTION, null);
        } catch (\Exception $e) {
            if ($this->environment === 'prod') {
                throw new \Exception(
                    'Unable to load the frontend.  Please try again or let the Ilios Team know about this issue:' .
                    $e->getMessage()
                );
            }

            print "Unable to load frontend.  Please run ilios:maintenance:update-frontend. \n";
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isOptional()
    {
        return true;
    }

    /**
     * @param string $cacheDir
     * @param string $environment
     * @param string $version
     *
     * @throws \Exception
     */
    protected function writeIndexFile($cacheDir, $environment, $version)
    {
        if (!$this->keepFrontendUpdated) {
            $version = $this->releaseVersion;
        }

        $contents = $this->builder->getIndex($environment, $version);
        if (!$contents) {
            throw new \Exception('Unable to build the index file');
        }

        $this->fs->dumpFile($cacheDir . '/' . self::CACHE_FILE_NAME, $contents);
    }
}
