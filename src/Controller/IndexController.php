<?php

namespace App\Controller;

use App\Service\Config;
use App\Service\Filesystem;
use App\Service\AuthenticationInterface;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Command\UpdateFrontendCommand;
use Twig\Environment;

class IndexController extends AbstractController
{
    const DEFAULT_TEMPLATE_NAME = 'index/webindex.html.twig';

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var Environment
     */
    protected $twig;

    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    private $kernelProjectDir;

    /**
     * IndexController constructor.
     * @param Filesystem $fs
     * @param Environment $twig
     * @param AuthenticationInterface $authentication
     * @param Config $config
     * @param string $kernelProjectDir
     */
    public function __construct(
        Filesystem $fs,
        Environment $twig,
        AuthenticationInterface $authentication,
        Config $config,
        string $kernelProjectDir
    ) {
        $this->fs = $fs;
        $this->twig = $twig;
        $this->authentication = $authentication;
        $this->config = $config;
        $this->kernelProjectDir = $kernelProjectDir;
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
        $response = $this->authentication->createAuthenticationResponse($request);
        if ($response instanceof RedirectResponse) {
            $crawlerDetect = new CrawlerDetect();
            if (!$crawlerDetect->isCrawler($request->headers->get('User-Agent'))) {
                return $response;
            }
        }

        $path = $this->getFilePath('index.json');
        if (!$path) {
            $response->setContent(
                $this->renderView('index/error.html.twig')
            );
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $response->headers->addCacheControlDirective('no-cache');
            $response->headers->addCacheControlDirective('no-store');
            $response->setMaxAge(1);
        } else {
            $options = $this->extractOptions($path);

            $content = $this->twig->render(self::DEFAULT_TEMPLATE_NAME, $options);
            $content = gzencode($content);
            $file = new \SplFileObject($path, 'r');
            $lastModified = \DateTime::createFromFormat('U', $file->getMTime());
            $response = $this->responseFromString($response, $content, $request, $lastModified);

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
        $response = new Response();
        $response = $this->responseFromString($response, $content, $request, $lastModified);

        $extension = $file->getExtension();
        if ($extension === 'css') {
            $response->headers->set('Content-Type', 'text/css');
        }
        if ($extension === 'js') {
            $response->headers->set('Content-Type', 'text/javascript');
        }
        if ($extension === 'svg') {
            $response->headers->set('Content-Type', 'image/svg+xml');
        }
        if ($extension === 'png') {
            //PNG files are already compressed and we don't gzip them again
            $response->headers->remove('Content-Encoding');
            $response->headers->set('Content-Type', 'image/png');
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
    protected function extractOptions($path)
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
                'isStyleSheet' => $obj->rel === 'stylesheet',
                'isNotStyleSheet' => $obj->rel !== 'stylesheet',
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
            'errorCaptureEnabled' => $this->config->get('errorCaptureEnabled')
        ];

        return $options;
    }

    protected function responseFromString(
        Response $response,
        string $content,
        Request $request,
        \DateTime $lastModified
    ) : Response {
        $response->setEtag(sha1($content));
        $response->setLastModified($lastModified);
        $response->setPublic();
        if (in_array('gzip', $request->getEncodings())) {
            $response->headers->add(['Content-Encoding' => 'gzip']);
        } else {
            $content = gzdecode($content);
        }
        $response->setContent($content);

        return $response;
    }
}
