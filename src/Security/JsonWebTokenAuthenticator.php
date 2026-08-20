<?php

declare(strict_types=1);

namespace App\Security;

use App\Classes\ServiceToken;
use App\Classes\ServiceTokenUserInterface;
use App\Classes\SessionUserInterface;
use App\Classes\UserToken;
use App\Service\ServiceTokenUserProvider;
use App\Service\TokenCodec;
use App\Service\TokenFactory;
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
use UnexpectedValueException;

class JsonWebTokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        protected TokenCodec $tokenCodec,
        protected TokenFactory $tokenFactory,
        protected RouterInterface $router,
        protected ServiceTokenUserProvider $tokenUserProvider,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        if (!$request->headers->has('X-JWT-Authorization')) {
            return false;
        }

        $authorizationHeader = $request->headers->get('X-JWT-Authorization');
        return (bool) preg_match('/^Token \S+$/', $authorizationHeader);
    }

    public function authenticate(Request $request): Passport
    {
        $authorizationHeader = $request->headers->get('X-JWT-Authorization');
        preg_match('/^Token (\S+)$/', $authorizationHeader, $matches);
        $jwt = $matches[1];
        try {
            $data = $this->tokenCodec->decode($jwt);
            $token = $this->tokenFactory->create($data);
            if ($token instanceof UserToken) {
                return $this->getPassportForUser($token);
            } elseif ($token instanceof ServiceToken) {
                return $this->getPassportForServiceToken($token);
            } else {
                throw new Exception('Cannot establish identity.');
            }
        } catch (UnexpectedValueException $e) {
            throw new CustomUserMessageAuthenticationException('Invalid JSON Web Token: ' . $e->getMessage());
        } catch (Exception) {
            throw new CustomUserMessageAuthenticationException('Invalid JSON Web Token');
        }
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new Response("Authentication Failed. " . $exception->getMessage(), 401);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // do nothing - continue with an authenticated user
        return null;
    }

    public function createToken(Passport $passport, string $firewallName): TokenInterface
    {
        $securityToken = parent::createToken($passport, $firewallName);
        $token = $passport->getAttribute('token');
        $securityToken->setAttribute('token', $token);
        return $securityToken;
    }

    /**
     * @throws CustomUserMessageAuthenticationException
     */
    protected function getPassportForUser(UserToken $token): Passport
    {
        $passport = new Passport(
            new UserBadge((string) $token->userId),
            new CustomCredentials(
                function (UserToken $token, UserInterface $user): bool {
                    assert($user instanceof SessionUserInterface);
                    if (!$user->isEnabled()) {
                        throw new CustomUserMessageAuthenticationException(
                            'Invalid JSON Web Token: user is disabled'
                        );
                    }
                    $tokenNotValidBefore = $user->tokenNotValidBefore();
                    if ($tokenNotValidBefore && $tokenNotValidBefore > $token->issuedAt) {
                        throw new CustomUserMessageAuthenticationException(
                            'Invalid JSON Web Token: Not issued after ' .
                            $tokenNotValidBefore->format('c') .
                            ' issued on ' . $token->issuedAt->format('c')
                        );
                    }
                    return true;
                },
                $token
            )
        );
        $passport->setAttribute('token', $token);
        return $passport;
    }

    protected function getPassportForServiceToken(ServiceToken $token): Passport
    {
        $passport = new Passport(
            new UserBadge(
                (string) $token->serviceTokenId,
                fn(string $identifier) => $this->tokenUserProvider->loadUserByIdentifier($identifier)
            ),
            new CustomCredentials(
                function (ServiceToken $token, UserInterface $user): bool {
                    assert($user instanceof ServiceTokenUserInterface);
                    if (!$user->isEnabled()) {
                        throw new CustomUserMessageAuthenticationException(
                            'Invalid JSON Web Token: service token is disabled'
                        );
                    }
                    return true;
                },
                $token
            )
        );
        $passport->setAttribute('token', $token);
        return $passport;
    }
}
