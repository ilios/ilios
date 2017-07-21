<?php

namespace Ilios\WebBundle\Controller;

use Ilios\CoreBundle\Service\Filesystem;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Ilios\CliBundle\Command\UpdateFrontendCommand;

class IndexController extends Controller
{
    public function indexAction()
    {
        $fs = $this->get(Filesystem::class);
        $path = $this->getParameter('kernel.cache_dir') . '/' . UpdateFrontendCommand::CACHE_FILE_NAME;

        if (!$fs->exists($path)) {
            $response = new Response(
                $this->renderView('IliosWebBundle:Index:error.html.twig')
            );
        } else {
            $contents = $fs->readFile($path);

            $response = new Response($contents);
        }

        $response->headers->set('Content-Type', 'text/html');

        $response->setPublic();
        $response->setMaxAge(60);

        return $response;
    }
}
