<?php

declare(strict_types=1);

namespace App\Controller;

use App\Classes\ServiceToken;
use App\Classes\ServiceTokenUserInterface;
use App\Classes\SessionUserInterface;
use App\Classes\UserToken;
use App\Repository\AuthenticationRepository;
use App\Repository\UserRepository;
use App\Service\AuthenticationInterface;
use App\Entity\UserInterface;
use App\Service\JsonWebTokenManager;
use App\Service\SessionUserProvider;
use App\Service\TokenCodec;
use App\Service\TokenFactory;
use App\Service\TokenManager;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use function sleep;

class AuthController extends AbstractController
{
    /**
     * Authenticate someone
     * Passes off the task of authentication to the service selected by the config
     * option authentication_type.
     */
    #[Route('/auth/login')]
    public function login(Request $request, AuthenticationInterface $authenticator): Response
    {
        return $authenticator->login($request);
    }

    /**
     * Get the id from the currently authenticated user
     */
    #[Route('/auth/whoami')]
    public function whoami(TokenStorageInterface $tokenStorage): JsonResponse
    {
        $token = $tokenStorage->getToken();
        $sessionUser = $token?->getUser();
        if (!$sessionUser instanceof SessionUserInterface) {
            throw new Exception('Attempted to access whoami with no valid user');
        }

        return new JsonResponse(['userId' => $sessionUser->getId()], Response::HTTP_OK);
    }

    /**
     * Get a new token
     * Useful when the time limit is approaching but the user is still active
     */
    #[Route('/auth/token')]
    public function token(
        Request $request,
        TokenStorageInterface $tokenStorage,
        TokenCodec $tokenCodec,
        TokenFactory $tokenFactory,
        TokenManager $tokenManager
    ): JsonResponse {
        $token = $tokenStorage->getToken();
        $sessionUser = $token?->getUser();
        if (!$sessionUser instanceof SessionUserInterface) {
            throw new Exception('Attempted to access token with no valid user');
        }

        $ttl = $request->query->get('ttl') ?: TokenManager::USER_TOKEN_DEFAULT_TTL;
        $userToken = $token->getAttribute('token');
        assert($userToken instanceof UserToken);
        $refreshedToken = $tokenManager->refreshUserToken($sessionUser, $userToken, $ttl);
        $jwt = $tokenCodec->encode($refreshedToken);
        return new JsonResponse(['jwt' => $jwt], Response::HTTP_OK);
    }

    /**
     * Get a new user token for the given user.
     * Requires authn with a service token carrying the proper permissions.
     */
    #[Route(
        '/auth/token/{userId}',
        requirements: [
            'userId' => '\d+',
        ],
        methods: ['GET'],
    )]
    public function userToken(
        int $userId,
        TokenStorageInterface $tokenStorage,
        UserRepository $userRepository,
        SessionUserProvider $sessionUserProvider,
        TokenManager $tokenManager,
        TokenCodec $tokenCodec,
    ): JsonResponse {
        $token = $tokenStorage->getToken();
        $sessionUser = $token?->getUser();

        // only allow service tokens to perform this action.
        if (!$sessionUser instanceof ServiceTokenUserInterface) {
            throw $this->createAccessDeniedException('Cannot create user token without a service token.');
        }
        $serviceToken = $token->getAttribute('token');
        assert($serviceToken instanceof ServiceToken);

        // authorization
        if (!$serviceToken->canCreateUserTokensFromToken) {
            throw $this->createAccessDeniedException('Insufficient permissions for creating user tokens.');
        }

        // look up the requested user account, make sure it's active.
        $user = $userRepository->findOneBy(['id' => $userId, 'enabled' => true]);
        if (! $user) {
            // let's keep this error message somewhat ambiguous on purpose, for security reasons.
            throw $this->createNotFoundException('Could not find the requested user.');
        }

        $sessionUser = $sessionUserProvider->createSessionUserFromUserId($user->getId());
        $userToken = $tokenManager->createUserTokenFromServiceToken($sessionUser, $serviceToken);
        $jwt = $tokenCodec->encode($userToken);

        return new JsonResponse(['jwt' => $jwt], Response::HTTP_OK);
    }

    /**
     * Logout
     * Passes off the task of logout to the service selected by the config
     * option authentication_type.
     */
    #[Route('/auth/logout')]
    public function logout(Request $request, AuthenticationInterface $authenticator): JsonResponse
    {
        return $authenticator->logout($request);
    }

    /**
     * Invalidate all tokens issued before now
     * Resets authentication in case a token is compromised
     *
     * @throws Exception
     */
    #[Route('/auth/invalidatetokens')]
    public function invalidateTokens(
        TokenStorageInterface $tokenStorage,
        UserRepository $userRepository,
        AuthenticationRepository $authenticationRepository,
        JsonWebTokenManager $jwtManager
    ): JsonResponse {
        $now = new DateTime();
        $token = $tokenStorage->getToken();
        $sessionUser = $token?->getUser();
        if (!$sessionUser instanceof SessionUserInterface) {
            throw new Exception('Attempted to access invalidate tokens with no valid user');
        }

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
