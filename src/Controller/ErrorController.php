<?php

declare(strict_types=1);

namespace App\Controller;

use App\Classes\SessionUserInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ErrorController extends AbstractController
{
    #[Route(
        '/errors',
        methods: ['POST'],
    )]
    public function postError(
        Request $request,
        LoggerInterface $logger,
        TokenStorageInterface $tokenStorage,
    ): Response {
        $sessionUser = $tokenStorage->getToken()?->getUser();
        if (!$sessionUser instanceof SessionUserInterface) {
            throw new AccessDeniedException('Unauthorized access!');
        }
        if ($request->request->has('data')) {
            $data = $request->request->all()['data'];
            $error = json_decode($data);
            $error->userId = $sessionUser->getId();
            $logger->error(json_encode($error));
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
