<?php
namespace Ilios\WebBundle\Service;

use Ilios\CoreBundle\Service\Fetch;
use Symfony\Component\Templating\EngineInterface;
use Exception;

class WebIndexFromJson
{
    /**
     * @var string
     */
    const DEFAULT_TEMPLATE_NAME = 'webindex.html.twig';
    const API_VERSION = 'v1.22';
    const AWS_BUCKET = 'https://s3-us-west-2.amazonaws.com/frontend-json-config/';

    const PRODUCTION = 'prod';
    const STAGING = 'stage';
    const DEVELOPMENT = 'dev';


    /**
     * @var EngineInterface
     */
    protected $templatingEngine;

    /**
     * @var Fetch
     */
    protected $fetch;

    /**
     * Construct
     * @param EngineInterface $templatingEngine
     * @param Fetch $fetch
     *
     */
    public function __construct(EngineInterface $templatingEngine, Fetch $fetch)
    {
        $this->templatingEngine = $templatingEngine;
        $this->fetch = $fetch;
    }

    /**
     * Get the index file as a string
     *
     * @param string $environment
     * @param string $version
     * @return string
     *
     * @throws \Exception when unable to access the version
     */
    public function getIndex($environment, $version = null)
    {
        $fileName = $environment . '-' . self::API_VERSION . '/index.json';
        if ($version) {
            $fileName .= ':' . $version;
        }

        $url = self::AWS_BUCKET . $fileName;
        $content = $this->fetch->get($url);

        $json = json_decode($content);

        $metas = array_map(function ($obj) {
            return [
                'name' => $obj->name,
                'content' => $obj->content
            ];
        }, $json->meta);

        $links = array_map(function ($obj) {
            return [
                'rel' => $obj->rel,
                'href' => $obj->href
            ];
        }, $json->link);

        $scripts = array_map(function ($obj) {
            return [
                'src' => property_exists($obj, 'src')?$obj->src:null,
                'content' => property_exists($obj, 'content')?$obj->content:null,
            ];
        }, $json->script);

        $options = [
            'metas' => $metas,
            'links' => $links,
            'scripts' => $scripts,
        ];

        $template = $this->getTemplatePath();

        $body = $this->templatingEngine->render($template, $options);

        if (!$body) {
            throw new Exception('Failed to create index file for version ' . $version);
        }

        return $body;
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
