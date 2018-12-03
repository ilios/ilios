<?php

namespace App\Security;

use App\Classes\SessionUserInterface;
use App\Service\JsonWebTokenManager;
use App\Service\SessionUserProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class JsonWebTokenAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var JsonWebTokenManager
     */
    protected $jwtManager;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * Constructor
     * @param JsonWebTokenManager $jwtManager
     * @param RouterInterface $router
     */
    public function __construct(JsonWebTokenManager $jwtManager, RouterInterface $router)
    {
        $this->jwtManager = $jwtManager;
        $this->router = $router;
    }

    /**
     * @inheritdoc
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $apiDocsUrl = $this->router->generate(
            'ilios_swagger_index',
            [],
            UrlGenerator::ABSOLUTE_URL
        );
        return new Response(
            'X-JWT-Authorization header required with JWT token. See ' .
            "<a href='${apiDocsUrl}'>${apiDocsUrl}</a>",
            401
        );
    }

    /**
     * @inheritdoc
     */
    public function supports(Request $request)
    {
        return $request->headers->has('X-JWT-Authorization');
    }

    /**
     * @inheritdoc
     */
    public function getCredentials(Request $request)
    {
        $authorizationHeader = $request->headers->get('X-JWT-Authorization');
        if (preg_match('/^Token (.*)$/', $authorizationHeader, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * @inheritdoc
     *
     * @param string $jwt the extracted JWT
     */
    public function getUser($jwt, UserProviderInterface $userProvider)
    {
        if (!$userProvider instanceof SessionUserProvider) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of SessionUserProvider (%s was given).',
                    get_class($userProvider)
                )
            );
        }

        try {
            $username = $this->jwtManager->getUserIdFromToken($jwt);
        } catch (\UnexpectedValueException $e) {
            throw new CustomUserMessageAuthenticationException('Invalid JSON Web Token: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new CustomUserMessageAuthenticationException('Invalid JSON Web Token');
        }

        /* @var SessionUserInterface $user */
        $user = $userProvider->loadUserByUsername($username);


        return $user;
    }

    /**
     * @inheritdoc
     *
     * @param string $jwt the extracted JWT
     */
    public function checkCredentials($jwt, UserInterface $user)
    {
        /** @var $user SessionUserInterface */
        if (!$user->isEnabled()) {
            throw new CustomUserMessageAuthenticationException(
                'Invalid JSON Web Token: user is disabled'
            );
        }

        $tokenNotValidBefore = $user->tokenNotValidBefore();
        $issuedAt = $this->jwtManager->getIssuedAtFromToken($jwt);
        if ($tokenNotValidBefore) {
            if ($tokenNotValidBefore > $issuedAt) {
                throw new CustomUserMessageAuthenticationException(
                    'Invalid JSON Web Token: Not issued after ' . $tokenNotValidBefore->format('c') .
                    ' issued on ' . $issuedAt->format('c')
                );
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response("Authentication Failed. " . $exception->getMessage(), 401);
    }

    /**
     * @inheritdoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // do nothing - continue with an authenticated user
    }

    /**
     * @inheritdoc
     */
    public function supportsRememberMe()
    {
        return false;
    }
}
