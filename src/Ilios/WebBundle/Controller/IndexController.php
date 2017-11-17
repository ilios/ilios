<?php

namespace Ilios\WebBundle\Controller;

use Http\Discovery\Exception\NotFoundException;
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
     * @param Request $request
     * @param $fileName
     * @param $versionedStaticFile
     *
     * @return BinaryFileResponse|Response
     */
    public function getAction(Request $request, $fileName, $versionedStaticFile = false)
    {
        if ('index.html' === $fileName || empty($fileName)) {
            return $this->getIndex($request);
        }

        $path = $this->getFilePath($fileName);
        //when files don't exist they are probably calls to a frontend route like /dashboard or /courses
        //in that case we just return the index.html file and let Ember handle it
        if (!$path) {
            return $this->getIndex($request);
        }

        return $this->getAsset($request, $path, $versionedStaticFile);
    }

    /**
     * Load the index.html file or a nice error message if it doesn't exist
     * @param Request $request
     * @return Response
     */
    protected function getIndex(Request $request)
    {
        $path = $this->getFilePath('index.json');
        if (!$path) {
            $response = new Response(
                $this->renderView('IliosWebBundle:Index:error.html.twig')
            );
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $response->headers->addCacheControlDirective('no-cache');
            $response->headers->addCacheControlDirective('no-store');
            $response->setMaxAge(1);
        } else {
            $options = $this->extractOptions($path);
            $templatePath = $this->getTemplatePath();

            $content = $this->templatingEngine->render($templatePath, $options);
            $content = gzencode($content);
            $file = new \SplFileObject($path, 'r');
            $lastModified = \DateTime::createFromFormat('U', $file->getMTime());
            $response = $this->responseFromString($content, $request, $lastModified);

            // doesn't actually mean don't cache - it means that the server must
            // check the status and if a 304 is returned it can use the cached version
            $response->headers->addCacheControlDirective('no-cache');
            $response->isNotModified($request);
        }

        return $response;
    }

    protected function getAsset(Request $request, string $path, bool $versionedStaticFile) : Response
    {
        $content = $this->fs->readFile($path);
        $file = new \SplFileObject($path, 'r');
        $lastModified = \DateTime::createFromFormat('U', $file->getMTime());
        $response = $this->responseFromString($content, $request, $lastModified);

        $extension = $file->getExtension();
        if ($extension === 'css') {
            $response->headers->set('Content-Type', 'text/css');
        }
        if ($extension === 'js') {
            $response->headers->set('Content-Type', 'text/javascript');
        }
        if ($versionedStaticFile) {
            //cache for one year
            $response->setMaxAge(60 * 60 * 24 * 365);
        } else {
            // doesn't actually mean don't cache - it means that the server must
            // check the status and if a 304 is returned it can use the cached version
            $response->headers->addCacheControlDirective('no-cache');
        }

        // checks if the file has been modified and if not blanks out the response and sends a 304
        $response->isNotModified($request);

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

        $filteredMetas = array_filter($json->meta, function ($obj) {
            return !property_exists($obj, 'name') or !(strncmp($obj->name, "iliosconfig", 11) === 0);
        });

        $metas = array_map(function ($obj) {
            return [
                'charset' => property_exists($obj, 'charset')?$obj->charset:null,
                'httpequiv' => property_exists($obj, 'http-equiv')?$obj->{'http-equiv'}:null,
                'name' => property_exists($obj, 'name')?$obj->name:null,
                'content' => property_exists($obj, 'content')?$obj->content:null,
            ];
        }, $filteredMetas);

        $links = array_map(function ($obj) {
            return [
                'rel' => $obj->rel,
                'preload' => $obj->rel === 'stylesheet',
                'href' => ltrim($obj->href, '/'),
                'sizes' => property_exists($obj, 'sizes')?$obj->sizes:null,
                'type' => property_exists($obj, 'type')?$obj->type:null,
            ];
        }, $json->link);

        $scripts = array_map(function ($obj) {
            return [
                'src' => property_exists($obj, 'src')?ltrim($obj->src, '/'):null,
                'content' => property_exists($obj, 'content')?$obj->content:null,
            ];
        }, $json->script);

        $styles = array_map(function ($obj) {
            return [
                'type' => $obj->type,
                'content' => $obj->content,
            ];
        }, $json->style);

        $noScripts = array_map(function ($obj) {
            return [
                'htmlContent' => $obj->htmlContent,
            ];
        }, $json->noScript);

        $divs = array_map(function ($obj) {
            return [
                'id' => $obj->id,
                'class' => $obj->class,
                'htmlContent' => $obj->htmlContent,
            ];
        }, $json->div);

        $options = [
            'metas' => $metas,
            'links' => $links,
            'scripts' => $scripts,
            'styles' => $styles,
            'noScripts' => $noScripts,
            'divs' => $divs,
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

    protected function responseFromString(string $content, Request $request, \DateTime $lastModified) : Response
    {
        $response = new Response();
        $acceptEncoding = $request->headers->get('Accept-Encoding');
        $response->setEtag(sha1($content));
        $response->setLastModified($lastModified);
        $response->setPublic();
        if (strpos($acceptEncoding, 'gzip') === false) {
            $content = gzdecode($content);
        } else {
            $response->headers->add(['Content-Encoding' => 'gzip']);
        }
        $response->setContent($content);

        return $response;
    }
}
