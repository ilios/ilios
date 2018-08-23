<?php

namespace AppBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends Controller
{
    /**
     * @param Request $request
     * @param LoggerInterface $logger
     * @return Response
     */
    public function errorAction(Request $request, LoggerInterface $logger)
    {
        if ($request->request->has('data')) {
            $data = $request->request->get('data');
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $error = json_decode($data);
            $error->userId = $user->getId();
            $logger->error(json_encode($error));
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
