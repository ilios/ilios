<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\UserInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    #[Route(
        '/errors',
        methods: ['POST'],
    )]
    public function postError(Request $request, LoggerInterface $logger): Response
    {
        if ($request->request->has('data')) {
            $data = $request->request->all()['data'];
            /** @var UserInterface $user */
            $user = $this->get('security.token_storage')->getToken()->getUser();
            $error = json_decode($data);
            $error->userId = $user->getId();
            $logger->error(json_encode($error));
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
