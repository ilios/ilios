<?php

namespace Ilios\ApiBundle\Service;

use Ilios\WebBundle\Service\WebIndexFromJson;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Request;


class SwaggerDocBuilder
{
    const CACHE_NAME = 'swagger-doc-builder.yaml';
    /**
     * @var string
     */
    protected $swaggerPaths;

    /**
     * @var string
     */
    protected $forceProtocol;

    /**
     * @var string
     */
    protected $environment;

    function __construct(KernelInterface $kernel, $forceProtocol)
    {
        $this->swaggerDir = $kernel->locateResource("@IliosApiBundle/Resources/swagger");
        $this->environment = $kernel->getEnvironment();
        $this->forceProtocol = $forceProtocol;
    }

    public function getDocs(Request $request)
    {
        $cache = new FilesystemAdapter();
        $cachedYaml = $cache->getItem(self::CACHE_NAME);

        if ($this->environment === 'dev' || !$cachedYaml->isHit()) {
            $paths = $this->getSection("paths");
            $definitions = $this->getSection("definitions");

            $swaggerDefinition = $this->getFrontMatter($request);
            $swaggerDefinition['paths'] = $paths;
            $swaggerDefinition['definitions'] = $definitions;

            $yaml = Yaml::dump($swaggerDefinition);

            $cachedYaml->set($yaml);
            $cache->save($cachedYaml);
        }

        return $cachedYaml->get();

    }

    /**
     * Parse a directory and its YAML files and convert them
     * into an array.
     *
     * @param string $dir
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function getSection($dir)
    {
        $finder = new Finder();
        $path = $this->swaggerDir . DIRECTORY_SEPARATOR . $dir;
        $files = $finder->in($path)->files()->name('*.yml')->sortByName();

        $items = [];
        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $contents = Yaml::parse($file->getContents());
            if (!is_array($contents)) {
                throw new \Exception(
                    "{$file->getRealPath()} is not valid YAML"
                );
            }

            $items = array_merge($items, $contents);
        }

        return $items;
    }

    /**
     * Get the information swagger loads at the top of the document
     *
     * @param Request $request
     *
     * @return array
     */
    protected function getFrontMatter(Request $request)
    {
        $arr = [];
        $arr['swagger'] = '2.0';
        $arr['info'] = [
            'title' => 'Ilios API',
            'description' => 'Ilios API Description',
            'version' => WebIndexFromJson::API_VERSION,
        ];

        $arr['host'] = $request->getHttpHost();
        $arr['schemes'] = [$this->forceProtocol];
        $arr['basePath'] = '/api/v1';
        $arr['produces'] = ['application/json'];

        return $arr;
    }
}