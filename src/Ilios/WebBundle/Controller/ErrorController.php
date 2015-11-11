<?php

namespace Ilios\WebBundle\Controller;

use FOS\RestBundle\Util\Codes;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends Controller
{
    public function errorAction(Request $request)
    {
        if ($request->request->has('data')) {
            $logger = $this->get('logger');
            $data = $request->request->get('data');
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $data['userId'] = $user->getId();
            $logger->error(json_encode($data));
        }

        return new Response('', Codes::HTTP_NO_CONTENT);
    }
}
