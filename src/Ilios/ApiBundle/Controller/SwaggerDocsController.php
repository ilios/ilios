<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\WebBundle\Service\WebIndexFromJson;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Finder\SplFileInfo;

class SwaggerDocsController extends Controller
{
    public function indexAction(Request $request)
    {
        $swaggerDir = $this->get('kernel')->locateResource("@IliosApiBundle/Resources/swagger");
        $paths = $this->getSection("{$swaggerDir}/paths");
        $definitions = $this->getSection("{$swaggerDir}/definitions");

        $swaggerDefinition = $this->getFrontMatter($request);
        $swaggerDefinition['paths'] = $paths;
        $swaggerDefinition['definitions'] = $definitions;

        $yaml = Yaml::dump($swaggerDefinition);

        $response = new Response(
            $yaml,
            Response::HTTP_OK,
            ['Content-type' => 'application/x-yaml']
        );

        return $response;
    }

    protected function getSection($dir)
    {
        $finder = new Finder();
        $files = $finder->in($dir)->files()->name('*.yml')->sortByName();

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
        $arr['schemes'] = [$this->container->getParameter('forceProtocol')];
        $arr['basePath'] = '/api/v1';
        $arr['produces'] = ['application/json'];

        return $arr;
    }

}