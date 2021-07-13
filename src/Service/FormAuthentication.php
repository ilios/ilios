<?php

declare(strict_types=1);

namespace App\Service;

use App\Classes\SessionUserInterface;
use App\Repository\AuthenticationRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\AuthenticationInterface as AuthenticationEntityInterface;
use App\Traits\AuthenticationService;

class FormAuthentication implements AuthenticationInterface
{
    use AuthenticationService;

    protected AuthenticationRepository $authenticationRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    protected $encoder;
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var JsonWebTokenManager
     */
    protected $jwtManager;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var SessionUserProvider
     */
    protected $sessionUserProvider;

    /**
     * Constructor
     */
    public function __construct(
        AuthenticationRepository $authenticationRepository,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $encoder,
        TokenStorageInterface $tokenStorage,
        JsonWebTokenManager $jwtManager,
        SessionUserProvider $sessionUserProvider
    ) {
        $this->authenticationRepository = $authenticationRepository;
        $this->encoder = $encoder;
        $this->tokenStorage = $tokenStorage;
        $this->jwtManager = $jwtManager;
        $this->userRepository = $userRepository;
        $this->sessionUserProvider = $sessionUserProvider;
    }

    /**
     * Login a user using a username and password
     *
     * @return JsonResponse
     */
    public function login(Request $request)
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
                    $passwordValid = $this->encoder->isPasswordValid($sessionUser, $password);
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
     *
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        return new JsonResponse([
            'status' => 'success'
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Update users to the new password encoding when they login
     * @param string $password
     */
    protected function updatePassword(
        AuthenticationEntityInterface $authEntity,
        SessionUserInterface $sessionUser,
        $password
    ) {
        if ($this->encoder->needsRehash($sessionUser)) {
            $newPassword = $this->encoder->encodePassword($sessionUser, $password);
            $authEntity->setPasswordHash($newPassword);
            $this->authenticationRepository->update($authEntity);
        }
    }

    /**
     * @inheritdoc
     */
    public function getPublicConfigurationInformation(Request $request)
    {
        $configuration = [];
        $configuration['type'] = 'form';

        return $configuration;
    }

    /**
     * @inheritdoc
     */
    public function createAuthenticationResponse(Request $request): Response
    {
        return new Response();
    }
}
