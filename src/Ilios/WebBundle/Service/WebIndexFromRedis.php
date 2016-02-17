<?php
namespace Ilios\WebBundle\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Templating\EngineInterface;
use Predis\Client;
use Exception;

class WebIndexFromRedis
{
    /**
     * @var string
     */
    const DEFAULT_TEMPLATE_NAME = 'webindex.html.twig';

    /**
     * @var Client
     */
    protected $redis;

    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * @var EngineInterface
     */
    protected $templatingEngine;

    /**
     * Construct
     * @param Client $redis
     * @param EngineInterface $templatingEngine
     * @param string $kernelCacheDir
     *
     */
    public function __construct(Client $redis, EngineInterface $templatingEngine, $kernelCacheDir)
    {
        $this->redis = $redis;
        $this->templatingEngine = $templatingEngine;
        $this->cacheDir = $kernelCacheDir;
    }


    /**
     * Get the index file as a string
     *
     * @param string $version
     * @return string
     *
     * @throws \Exception when unable to access the version
     */
    public function getIndex($version)
    {
        $fileName = 'ilios:index:' . $version;
        $fs = new Filesystem();
        $cacheLocation = $this->cacheDir . '/ilios/' . $fileName;

        if ($fs->exists($cacheLocation)) {
            return file_get_contents($cacheLocation);
        }

        $content = $this->redis->get('ilios:index:' . $version);
        if (!$content) {
            throw new Exception('Failed to get contents from redis for version ' . $version);
        }

        $json = json_decode($content);

        $metas = array_map(function($obj){
            return [
                'name' => $obj->name,
                'content' => $obj->content
            ];
        }, $json->meta);

        $links = array_map(function($obj){
            return [
                'rel' => $obj->rel,
                'href' => $obj->href
            ];
        }, $json->link);

        $scripts = array_map(function($obj){
            return $obj->src;
        }, $json->script);

        $options = [
            'base_url' => $json->base[0]->href,
            'metas' => $metas,
            'links' => $links,
            'scripts' => $scripts,
        ];

        $template = $this->getTemplatePath();

        $body = $this->templatingEngine->render($template, $options);

        if (!$body) {
            throw new Exception('Failed to create index file for version ' . $version);
        }
        $fs->dumpFile($cacheLocation, $body);

        return $body;
    }


    /**
     * Get the index file as a string
     *
     * @param string $version
     * @return string
     *
     * @throws \Exception when unable to access the version
     */
    public function clearCache($version)
    {
        $fileName = 'ilios:index:' . $version;
        $fs = new Filesystem();
        $cacheLocation = $this->cacheDir . '/ilios/' . $fileName;

        if ($fs->exists($cacheLocation)) {
            $fs->remove($cacheLocation);
        }
    }


    /**
     * Locates the applicable template and returns its path.
     * @return string The template path.
     */
    protected function getTemplatePath()
    {
        $paths = [
            '@custom_webindex_templates/' . self::DEFAULT_TEMPLATE_NAME,
        ];
        foreach ($paths as $path) {
            if ($this->templatingEngine->exists($path)) {
                return $path;
            }
        }
        return 'IliosWebBundle:WebIndex:' .self::DEFAULT_TEMPLATE_NAME;
    }
}
