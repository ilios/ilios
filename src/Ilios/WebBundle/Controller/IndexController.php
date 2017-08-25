<?php

namespace Ilios\WebBundle\Controller;

use Ilios\CoreBundle\Service\Filesystem;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ilios\CliBundle\Command\UpdateFrontendCommand;
use Symfony\Component\Templating\EngineInterface;

class IndexController extends Controller
{
    const DEFAULT_TEMPLATE_NAME = 'webindex.html.twig';

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var EngineInterface
     */
    protected $templatingEngine;

    /**
     * IndexController constructor.
     * @param Filesystem $fs
     * @param EngineInterface $templatingEngine
     */
    public function __construct(Filesystem $fs, EngineInterface $templatingEngine)
    {
        $this->fs = $fs;
        $this->templatingEngine = $templatingEngine;
    }

    /**
     * Respond to GET requests
     *
     * @return Response
     */
    public function getAction(Request $request, $fileName)
    {
        if ('index.html' === $fileName || empty($fileName)) {
            return $this->getIndex();
        }

        $path = $this->getFilePath($fileName);
        //when files don't exist they are probably calls to a frontend route like /dashboard or /courses
        //in that case we just return the index.html file and let Ember handle it
        if (!$path) {
            return $this->getIndex();
        }

        $response = new BinaryFileResponse($path);
        $response->setAutoLastModified();
        $response->setAutoEtag();
        // checks if the file has been modified and if not blanks out the response and sends a 304
        $response->isNotModified($request);

        $file = $response->getFile();
        $extension = $file->getExtension();
        //assets which are gzipped by the ember build process
        if (in_array($extension, ['css', 'js'])) {
            $response->headers->set('Content-Encoding', 'gzip');
            if ($extension === 'css') {
                $response->headers->set('Content-Type', 'text/css');
            }
            if ($extension === 'js') {
                $response->headers->set('Content-Type', 'text/javascript');
            }
        }

        return $response;
    }

    /**
     * Load the index.html file or a nice error message if it doesn't exist
     * @return Response
     */
    protected function getIndex()
    {
        $path = $this->getFilePath('index.json');
        if (!$path) {
            $response = new Response(
                $this->renderView('IliosWebBundle:Index:error.html.twig')
            );
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
        } else {
            $options = $this->extractOptions($path);
            $templatePath = $this->getTemplatePath();

            $response = $this->render($templatePath, $options);
        }
        
        $response->setPublic();
        $response->setMaxAge(60);

        return $response;
    }

    /**
     * Extract the path for a frontend file
     * @param $fileName
     * @return bool|string
     */
    protected function getFilePath($fileName)
    {
        $assetsPath = $this->getParameter('kernel.cache_dir') . UpdateFrontendCommand::FRONTEND_DIRECTORY;
        $path = $assetsPath . $fileName;
        if ($this->fs->exists($path)) {
            return $path;
        }

        return false;
    }


    /**
     * Extract the data from index.json file
     * @param string $path
     * @return array
     */
    public function extractOptions($path)
    {
        $contents = $this->fs->readFile($path);
        $json = json_decode($contents);

        $metas = array_map(function ($obj) {
            return [
                'name' => $obj->name,
                'content' => $obj->content
            ];
        }, $json->meta);

        $links = array_map(function ($obj) {
            return [
                'rel' => $obj->rel,
                'href' => ltrim($obj->href, '/')
            ];
        }, $json->link);

        $scripts = array_map(function ($obj) {
            return [
                'src' => property_exists($obj, 'src')?ltrim($obj->src, '/'):null,
                'content' => property_exists($obj, 'content')?$obj->content:null,
            ];
        }, $json->script);

        $options = [
            'metas' => $metas,
            'links' => $links,
            'scripts' => $scripts,
        ];

        return $options;
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
