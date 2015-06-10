<?php
namespace Ilios\WebBundle\Service;

class WebAssets
{
    protected $environment;
    protected $bucket;
    protected $version;

    /**
     * Get the configuration options
     * @param [string] $environment
     * @param [string] $version
     */
    public function __construct($environment, $version, $bucket)
    {
        $this->bucket = $bucket;
        $this->environment = $environment;
        $this->version = $version;
    }

    /**
     * Get the string contents of a remote file
     * @param  string $fileName the file we are fetching
     * @return string
     */
    protected function getFileString($fileName)
    {
        $opts = array(
            'http'=>array(
                'method'=>"GET"
            )
        );
        $context = stream_context_create($opts);

        return file_get_contents($this->bucket . $fileName, false, $context);
    }

    /**
     * Get the index file as a string
     * @return string
     */
    public function getIndex()
    {
        $fileName = $this->version?'ilios:' . $this->version:'index';
        return $this->getFileString($fileName . '.html');
    }
}
