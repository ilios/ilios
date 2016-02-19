<?php

namespace Ilios\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Ilios\CliBundle\Command\UpdateFrontendCommand;

use Ilios\WebBundle\Service\WebIndexFromJson;

class IndexController extends Controller
{
    public function indexAction()
    {
        $fs = $this->get('ilioscore.symfonyfilesystem');
        $path = $this->getParameter('kernel.cache_dir') . '/' . UpdateFrontendCommand::CACHE_FILE_NAME;

        if (!$fs->exists($path)) {
            throw new \Exception(
                "Unable to load the index file at {$path}.  Run ilios:maintenance:update-frontend to create it."
            );
        }
        $contents = $fs->readFile($path);

        $response = new Response($contents);
        $response->headers->set('Content-Type', 'text/html');

        $response->setPublic();
        $response->setMaxAge(60);

        return $response;
    }
}
