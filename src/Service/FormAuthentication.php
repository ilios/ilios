<?php

declare(strict_types=1);

namespace App\Service;

use App\Classes\SessionUserInterface;
use App\Repository\AuthenticationRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\AuthenticationInterface as AuthenticationEntityInterface;
use App\Traits\AuthenticationService;

class FormAuthentication implements AuthenticationInterface
{
    use AuthenticationService;

    public function __construct(
        protected AuthenticationRepository $authenticationRepository,
        protected UserRepository $userRepository,
        protected UserPasswordHasherInterface $hasher,
        protected TokenStorageInterface $tokenStorage,
        protected JsonWebTokenManager $jwtManager,
        protected SessionUserProvider $sessionUserProvider
    ) {
    }

    /**
     * Login a user using a username and password
     */
    public function login(Request $request): JsonResponse
    {
        $username = null;
        $password = null;
        $content = $request->getContent();
        if (!empty($content)) {
            $arr = json_decode($content, true);
            if (array_key_exists('username', $arr)) {
                $username = $arr['username'];
            }
            if (array_key_exists('password', $arr)) {
                $password = $arr['password'];
            }
        }
        $code = JsonResponse::HTTP_OK;
        $errors = [];
        if (!$username) {
            $errors[] = 'missingUsername';
            $code = JsonResponse::HTTP_BAD_REQUEST;
        }
        if (!$password) {
            $errors[] = 'missingPassword';
            $code = JsonResponse::HTTP_BAD_REQUEST;
        }

        if ($username && $password) {
            $authEntity = $this->authenticationRepository->findOneByUsername($username);
            if ($authEntity) {
                $sessionUser = $this->sessionUserProvider->createSessionUserFromUser($authEntity->getUser());
                if ($sessionUser->isEnabled()) {
                    $passwordValid = $this->hasher->isPasswordValid($sessionUser, $password);
                    if ($passwordValid) {
                        $this->updatePassword($authEntity, $sessionUser, $password);
                        $jwt = $this->jwtManager->createJwtFromSessionUser($sessionUser);

                        return $this->createSuccessResponseFromJWT($jwt);
                    }
                }
            }
            $errors[] = 'badCredentials';
            $code = JsonResponse::HTTP_UNAUTHORIZED;
        }

        return new JsonResponse([
            'status' => 'error',
            'errors' => $errors,
            'jwt' => null,
        ], $code);
    }

    /**
     * Logout a user
     */
    public function logout(Request $request): JsonResponse
    {
        return new JsonResponse([
            'status' => 'success',
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Update users to the new password encoding when they login
     */
    protected function updatePassword(
        AuthenticationEntityInterface $authEntity,
        SessionUserInterface $sessionUser,
        string $password
    ): void {
        if ($this->hasher->needsRehash($sessionUser)) {
            $newPassword = $this->hasher->hashPassword($sessionUser, $password);
            $authEntity->setPasswordHash($newPassword);
            $this->authenticationRepository->update($authEntity);
        }
    }

    public function getPublicConfigurationInformation(Request $request): array
    {
        $configuration = [];
        $configuration['type'] = 'form';

        return $configuration;
    }

    public function createAuthenticationResponse(Request $request): Response
    {
        return new Response();
    }
}
