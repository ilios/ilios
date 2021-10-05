<?php

declare(strict_types=1);

namespace App\Controller;

use App\Classes\SessionUserInterface;
use App\Repository\AuthenticationRepository;
use App\Repository\UserRepository;
use App\Service\AuthenticationInterface;
use App\Service\JsonWebTokenManager;
use App\Entity\UserInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AuthController extends AbstractController
{
    /**
     * Authenticate someone
     * Passes off the task of authentication to the service selected by the config
     * option authentication_type.
     */
    public function loginAction(Request $request, AuthenticationInterface $authenticator): Response
    {
        return $authenticator->login($request);
    }

    /**
     * Get the id from the currently authenticated user
     */
    public function whoamiAction(TokenStorageInterface $tokenStorage): JsonResponse
    {
        $token = $tokenStorage->getToken();
        if ($token?->isAuthenticated()) {
            /** @var SessionUserInterface $sessionUser */
            $sessionUser = $token->getUser();
            if ($sessionUser instanceof SessionUserInterface) {
                return new JsonResponse(['userId' => $sessionUser->getId()], JsonResponse::HTTP_OK);
            }
        }

        return new JsonResponse(['userId' => null], Response::HTTP_OK);
    }

    /**
     * Get a new token
     * Useful when the time limit is approaching but the user is still active
     */
    public function tokenAction(
        Request $request,
        TokenStorageInterface $tokenStorage,
        JsonWebTokenManager $jwtManager
    ): JsonResponse {
        $token = $tokenStorage->getToken();
        if ($token?->isAuthenticated()) {
            $sessionUser = $token->getUser();
            if ($sessionUser instanceof SessionUserInterface) {
                $ttl = $request->get('ttl') ? $request->get('ttl') : 'PT8H';
                $jwt = $jwtManager->createJwtFromSessionUser($sessionUser, $ttl);
                return new JsonResponse(['jwt' => $jwt], Response::HTTP_OK);
            }
        }

        return new JsonResponse(['jwt' => null], JsonResponse::HTTP_OK);
    }

    /**
     * Logout
     * Passes off the task of logout to the service selected by the config
     * option authentication_type.
     */
    public function logoutAction(Request $request, AuthenticationInterface $authenticator): JsonResponse
    {
        return $authenticator->logout($request);
    }

    /**
     * Invalidate all tokens issued before now
     * Resets authentication in case a token is compromised
     *
     * @throws Exception
     */
    public function invalidateTokensAction(
        TokenStorageInterface $tokenStorage,
        UserRepository $userRepository,
        AuthenticationRepository $authenticationRepository,
        JsonWebTokenManager $jwtManager
    ): JsonResponse {
        $now = new \DateTime();
        $token = $tokenStorage->getToken();
        if ($token?->isAuthenticated()) {
            /** @var SessionUserInterface $sessionUser */
            $sessionUser = $token->getUser();
            if ($sessionUser instanceof SessionUserInterface) {
                /** @var UserInterface $user */
                $user = $userRepository->findOneBy(['id' => $sessionUser->getId()]);
                $authentication = $authenticationRepository->findOneBy(['user' => $user->getId()]);
                if (!$authentication) {
                    $authentication = $authenticationRepository->create();
                    $authentication->setUser($user);
                }

                $authentication->setInvalidateTokenIssuedBefore($now);
                $authenticationRepository->update($authentication);

                sleep(1);
                $jwt = $jwtManager->createJwtFromSessionUser($sessionUser);

                return new JsonResponse(['jwt' => $jwt], Response::HTTP_OK);
            }
        }

        throw new Exception('Attempted to invalidate token with no valid user');
    }
}
