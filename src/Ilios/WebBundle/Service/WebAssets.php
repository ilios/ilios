<?php
namespace Ilios\WebBundle\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

class WebAssets
{
    protected $environment;
    protected $bucket;
    protected $version;
    protected $cacheDir;

    /**
     * Get the configuration options
     * @param string $environment
     * @param string $version
     * @param string $version
     */
    public function __construct($environment, $version, $bucket, $kernelCacheDir)
    {
        $this->bucket = $bucket;
        $this->environment = $environment;
        $this->version = $version;
        $this->cacheDir = $kernelCacheDir;
    }

    /**
     * Get the string contents of a remote file
     * @param  string $fileName the file we are fetching
     * @return string
     */
    protected function getFileString($fileName)
    {
        $fs = new Filesystem();
        $cacheLocation = $this->cacheDir . '/ilios/' . $fileName;

        if ($fs->exists($cacheLocation)) {
            return file_get_contents($cacheLocation);
        }

        $opts = array(
            'http'=>array(
                'method'=>"GET"
            )
        );
        $context = stream_context_create($opts);
        $fileContents = file_get_contents($this->bucket . $fileName, false, $context);

        $fs->dumpFile($cacheLocation, $fileContents);

        return $fileContents;
    }


    /**
     * Get the index file as a string
     * @return string
     */
    public function getIndex()
    {
        $fileName = $this->version?'index.html:' . $this->version:'index.html';
        return $this->getFileString($fileName);
    }
}
