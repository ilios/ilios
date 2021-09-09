<?php

declare(strict_types=1);

namespace App\Security;

use App\Classes\SessionUserInterface;
use App\Service\JsonWebTokenManager;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use UnexpectedValueException;

class JsonWebTokenAuthenticator extends AbstractAuthenticator
{
    /**
     * Constructor
     */
    public function __construct(protected JsonWebTokenManager $jwtManager, protected RouterInterface $router)
    {
    }

    /**
     * @inheritdoc
     */
    public function supports(Request $request): ?bool
    {
        if (!$request->headers->has('X-JWT-Authorization')) {
            return false;
        }

        $authorizationHeader = $request->headers->get('X-JWT-Authorization');
        return preg_match('/^Token \S+$/', $authorizationHeader);
    }

    public function authenticate(Request $request): PassportInterface
    {
        $authorizationHeader = $request->headers->get('X-JWT-Authorization');
        preg_match('/^Token (\S+)$/', $authorizationHeader, $matches);

        if (preg_match('/^Token (\S+)$/', $authorizationHeader, $matches)) {
            $jwt = $matches[1];
            try {
                $username = $this->jwtManager->getUserIdFromToken($jwt);
                return new Passport(
                    new UserBadge($username),
                    new CustomCredentials(
                        function ($credentials, UserInterface $user) {
                            /* @var SessionUserInterface $user */
                            if (!$user->isEnabled()) {
                                throw new CustomUserMessageAuthenticationException(
                                    'Invalid JSON Web Token: user is disabled'
                                );
                            }
                            $tokenNotValidBefore = $user->tokenNotValidBefore();
                            $issuedAt = $this->jwtManager->getIssuedAtFromToken($credentials);
                            if ($tokenNotValidBefore) {
                                if ($tokenNotValidBefore > $issuedAt) {
                                    throw new CustomUserMessageAuthenticationException(
                                        'Invalid JSON Web Token: Not issued after ' .
                                        $tokenNotValidBefore->format('c') .
                                        ' issued on ' . $issuedAt->format('c')
                                    );
                                }
                            }
                            return true;
                        },
                        $jwt
                    )
                );
            } catch (UnexpectedValueException $e) {
                throw new CustomUserMessageAuthenticationException('Invalid JSON Web Token: ' . $e->getMessage());
            } catch (Exception) {
                throw new CustomUserMessageAuthenticationException('Invalid JSON Web Token');
            }
        }
        throw new CustomUserMessageAuthenticationException('No JSON Web Token provided');
    }


    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response("Authentication Failed. " . $exception->getMessage(), 401);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        // do nothing - continue with an authenticated user
        return null;
    }
}
