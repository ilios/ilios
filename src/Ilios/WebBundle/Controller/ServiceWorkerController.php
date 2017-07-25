<?php

namespace Ilios\WebBundle\Controller;

use Ilios\CoreBundle\Service\Filesystem;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Ilios\CliBundle\Command\UpdateServiceWorkerCommand;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ServiceWorkerController extends Controller
{
    public function swJsAction(Filesystem $fs)
    {
        $path = $this->getParameter('kernel.cache_dir') . '/' . UpdateServiceWorkerCommand::SWJS_CACHE_FILE_NAME;

        if (!$fs->exists($path)) {
            throw new NotFoundHttpException('sw.js has not been loaded. ' .
                'Please run ilios:maintenance:update-serviceworker to load it');
        }

        $contents = $fs->readFile($path);
        $response = new Response($contents);
        $response->headers->set('Content-Type', 'application/javascript');
        $response->headers->set('Content-Encoding', 'gzip');

        $response->setPublic();

        return $response;
    }

    public function swRegistrationJsAction(Filesystem $fs)
    {
        $path = $this->getParameter('kernel.cache_dir') .
            '/' .
            UpdateServiceWorkerCommand::SW_REGISTRATION_CACHE_FILE_NAME;

        if (!$fs->exists($path)) {
            throw new NotFoundHttpException('sw-registration.js has not been loaded. ' .
                'Please run ilios:maintenance:update-serviceworker to load it');
        }

        $contents = $fs->readFile($path);
        $response = new Response($contents);
        $response->headers->set('Content-Type', 'application/javascript');
        $response->headers->set('Content-Encoding', 'gzip');

        $response->setPublic();

        return $response;
    }
}
