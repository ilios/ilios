<?php

declare(strict_types=1);

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
use DateTime;

class IndexController extends AbstractController
{
    private const DEFAULT_TEMPLATE_NAME = 'index/webindex.html.twig';

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var Environment
     */
    protected $twig;

    /**
     * @var string
     */
    protected $kernelCacheDir;

    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    /**
     * @var Config
     */
    private $config;

    /**
     * IndexController constructor.
     */
    public function __construct(
        Filesystem $fs,
        Environment $twig,
        AuthenticationInterface $authentication,
        Config $config,
        string $kernelCacheDir
    ) {
        $this->fs = $fs;
        $this->twig = $twig;
        $this->authentication = $authentication;
        $this->config = $config;
        $this->kernelCacheDir = $kernelCacheDir;
    }

    /**
     * Load the index.html file or a nice error message if it doesn't exist
     */
    public function index(Request $request): Response
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
            $file = new \SplFileObject($path, 'r');
            $lastModified = new DateTime();
            $lastModified->setTimestamp($file->getMTime());
            $response = $this->responseFromString($response, $content, $request, $lastModified);

            // doesn't actually mean don't cache - it means that the server must
            // check the status and if a 304 is returned it can use the cached version
            $response->headers->addCacheControlDirective('no-cache');
            $response->isNotModified($request);
        }

        return $response;
    }

    /**
     * Extract the path for a frontend file
     * @param $fileName
     * @return bool|string
     */
    protected function getFilePath($fileName)
    {
        $assetsPath = $this->kernelCacheDir . UpdateFrontendCommand::FRONTEND_DIRECTORY;
        $path = $assetsPath . $fileName;
        if ($this->fs->exists($path) && is_readable($path) && !is_dir($path)) {
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
                'charset' => property_exists($obj, 'charset') ? $obj->charset : null,
                'httpequiv' => property_exists($obj, 'http-equiv') ? $obj->{'http-equiv'} : null,
                'name' => property_exists($obj, 'name') ? $obj->name : null,
                'content' => property_exists($obj, 'content') ? $obj->content : null,
            ];
        }, $filteredMetas);

        $stylesheets = [];
        $preloadLinks = [];
        $links = [];
        foreach ($json->link as $obj) {
            $arr = [
                'href' => ltrim($obj->href, '/'),
            ];
            switch ($obj->rel) {
                case 'preload':
                    if (property_exists($obj, 'as') && property_exists($obj, 'crossorigin')) {
                        $arr['as'] = $obj->as;
                        $arr['crossorigin'] = $obj->crossorigin;
                        $arr['type'] = property_exists($obj, 'type') ? $obj->type : false;
                        $preloadLinks[] = $arr;
                    }
                    break;
                case 'stylesheet':
                    $stylesheets[] = $arr;
                    break;
                default:
                    $arr['sizes'] = property_exists($obj, 'sizes') ? $obj->sizes : false;
                    $arr['type'] = property_exists($obj, 'type') ? $obj->type : false;
                    $arr['rel'] = $obj->rel;
                    $links[] = $arr;
            }
        }

        $scripts = array_map(function ($obj) {
            return [
                'src' => property_exists($obj, 'src') ? ltrim($obj->src, '/') : null,
                'content' => property_exists($obj, 'content') ? $obj->content : null,
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

        return [
            'metas' => $metas,
            'stylesheets' => $stylesheets,
            'preloadLinks' => $preloadLinks,
            'links' => $links,
            'scripts' => $scripts,
            'styles' => $styles,
            'noScripts' => $noScripts,
            'divs' => $divs,
            'errorCaptureEnabled' => $this->config->get('errorCaptureEnabled')
        ];
    }

    protected function responseFromString(
        Response $response,
        string $content,
        Request $request,
        \DateTime $lastModified
    ): Response {
        $response->setEtag(sha1($content));
        $response->setLastModified($lastModified);
        $response->setPublic();
        if (in_array('gzip', $request->getEncodings())) {
            $content = gzencode($content);
            $response->headers->add(['Content-Encoding' => 'gzip']);
        }
        $response->setContent($content);

        return $response;
    }
}
