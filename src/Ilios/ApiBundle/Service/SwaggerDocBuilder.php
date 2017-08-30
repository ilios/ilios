<?php

namespace Ilios\ApiBundle\Service;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Templating\EngineInterface;

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
    protected $environment;

    /**
     * @var EngineInterface
     */
    protected $templatingEngine;

    /**
     * @var string
     */
    protected $apiVersion;

    /**
     * @var Router
     */
    protected $router;

    public function __construct(
        KernelInterface $kernel,
        EngineInterface $templatingEngine,
        Router $router,
        $apiVersion
    ) {
        $this->swaggerDir = $kernel->locateResource("@IliosApiBundle/Resources/swagger");
        $this->environment = $kernel->getEnvironment();
        $this->templatingEngine = $templatingEngine;
        $this->router = $router;
        $this->apiVersion = $apiVersion;
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
            'title' => 'Ilios API Documentation',
            'description' => $this->getDescription(),
            'version' => $this->apiVersion,
        ];

        $arr['host'] = $request->getHttpHost();
        $arr['schemes'] = ['https'];
        $arr['basePath'] = '/api/v1';
        $arr['produces'] = ['application/json'];

        return $arr;
    }

    protected function getDescription()
    {
        $apiDocsUrl = $this->router->generate(
            'ilios_swagger_index',
            [],
            UrlGenerator::ABSOLUTE_URL
        );
        $myprofileUrl = $this->router->generate(
            'ilios_web_assets',
            [],
            UrlGenerator::ABSOLUTE_URL
        );
        $userApiUrl = $this->router->generate(
            'ilios_api_getall',
            ['version' => 'v1', 'object' => 'users'],
            UrlGenerator::ABSOLUTE_URL
        );
        $template = 'IliosApiBundle:swagger:description.markdown.twig';
        return $this->templatingEngine->render($template, [
            'apiDocsUrl' => $apiDocsUrl,
            'myprofileUrl' => $myprofileUrl . 'myprofile',
            'userApiUrl' => $userApiUrl,
        ]);
    }
}
