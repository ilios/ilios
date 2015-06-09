<?php

namespace Ilios\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller
{
    public function indexAction()
    {
        $opts = array(
            'http'=>array(
                'method'=>"GET"
            )
        );

        $context = stream_context_create($opts);

        // Open the file using the HTTP headers set above
        $file = file_get_contents('https://s3-us-west-1.amazonaws.com/iliosindex/index.html', false, $context);

        $response = new Response($file);
        $response->headers->set('Content-Type', 'text/html');

        return $response;
    }
}
