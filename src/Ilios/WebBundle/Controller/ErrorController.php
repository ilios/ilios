<?php

namespace Ilios\WebBundle\Controller;

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
            $error = json_decode($data);
            $error->userId = $user->getId();
            $logger->error(json_encode($error));
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
