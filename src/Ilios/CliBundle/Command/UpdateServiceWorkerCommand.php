<?php

namespace Ilios\CliBundle\Command;

use Ilios\CoreBundle\Service\Fetch;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

use Ilios\CoreBundle\Service\Filesystem;

/**
 * Pull down service worker JS files from cloudfront
 * So they can be served from the API host since service workers
 * won't work from a different domain
 *
 * Class UpdateServiceWorkerCommand
 * @package Ilios\CliBUndle\Command
 */
class UpdateServiceWorkerCommand extends Command implements CacheWarmerInterface
{
    /**
     * @var string
     */
    const SWJS_CACHE_FILE_NAME = '/ilios/sw.js';
    const SW_REGISTRATION_CACHE_FILE_NAME = '/ilios/sw-registration.js';
    const PRODUCTION = 'prod';
    const STAGING = 'stage';
    const DEVELOPMENT = 'dev';
    const CDN_ASSET_DOMAIN = 'https://d26vzvixg52o0d.cloudfront.net/';

    /**
     * @var Fetch
     */
    protected $fetch;

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
    protected $environment;
    
    public function __construct(
        Fetch $fetch,
        Filesystem $fs,
        $kernelCacheDir,
        $environment
    ) {
        $this->fetch = $fetch;
        $this->fs = $fs;
        $this->cacheDir = $kernelCacheDir;
        $this->environment = $environment;

        parent::__construct();
    }
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ilios:maintenance:update-serviceworker')
            ->setDescription('Updates the service worker javascript to the latest version.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->writeServiceWorkerFile($this->cacheDir);

        $message = 'Service worker updated successfully!';

        $output->writeln("<info>{$message}</info>");
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir)
    {
        try {
            $this->writeServiceWorkerFile($cacheDir);
        } catch (\Exception $e) {
            if ($this->environment === 'prod') {
                throw new \Exception(
                    'Unable to load the service worker.  ' .
                    'Please try again or let the Ilios Team know about this issue.'
                );
            }

            print "Unable to load service worker.  Please run ilios:maintenance:update-serviceworker. \n";
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
     *
     * @throws \Exception
     */
    protected function writeServiceWorkerFile($cacheDir)
    {
        $swJs = $this->fetch->get(self::CDN_ASSET_DOMAIN . 'sw.js');
        $this->fs->dumpFile($cacheDir . self::SWJS_CACHE_FILE_NAME, gzencode($swJs));

        $swRegistrationJs = $this->fetch->get(self::CDN_ASSET_DOMAIN . 'sw-registration.js');
        $this->fs->dumpFile($cacheDir . self::SW_REGISTRATION_CACHE_FILE_NAME, gzencode($swRegistrationJs));
    }
}
