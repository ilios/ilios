<?php

declare(strict_types=1);

namespace App\Security;

use App\Classes\ServiceTokenUserInterface;
use App\Classes\SessionUserInterface;
use App\Service\JsonWebTokenManager;
use App\Service\ServiceTokenUserProvider;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use UnexpectedValueException;

class JsonWebTokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        protected JsonWebTokenManager $jwtManager,
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
        $token = $matches[1];
        try {
            if ($this->jwtManager->isUserToken($token)) {
                return $this->getPassportForUser($token);
            } elseif ($this->jwtManager->isServiceToken($token)) {
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
        $token = parent::createToken($passport, $firewallName);
        $jwt = $passport->getAttribute('jwt');
        $token->setAttribute('jwt', $jwt);
        if ($this->jwtManager->isServiceToken($jwt)) {
            $token->setAttribute(
                JsonWebTokenManager::WRITEABLE_SCHOOLS_KEY,
                $this->jwtManager->getWriteableSchoolIdsFromToken($jwt)
            );
        }
        return $token;
    }

    /**
     * @throws CustomUserMessageAuthenticationException
     */
    protected function getPassportForUser(string $jwt): Passport
    {
        $userId = $this->jwtManager->getUserIdFromToken($jwt);
        $passport = new Passport(
            new UserBadge((string) $userId),
            new CustomCredentials(
                function ($token, SessionUserInterface $user) {
                    if (!$user->isEnabled()) {
                        throw new CustomUserMessageAuthenticationException(
                            'Invalid JSON Web Token: user is disabled'
                        );
                    }
                    $tokenNotValidBefore = $user->tokenNotValidBefore();
                    $issuedAt = $this->jwtManager->getIssuedAtFromToken($token);
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
        $passport->setAttribute('jwt', $jwt);
        return $passport;
    }

    protected function getPassportForServiceToken(string $jwt): Passport
    {
        $tokenId = $this->jwtManager->getServiceTokenIdFromToken($jwt);
        $schoolIds = $this->jwtManager->getWriteableSchoolIdsFromToken($jwt);
        $passport = new Passport(
            new UserBadge(
                (string) $tokenId,
                fn(string $identifier) => $this->tokenUserProvider->loadUserByIdentifier($identifier)
            ),
            new CustomCredentials(
                function ($token, ServiceTokenUserInterface $user) {
                    if (!$user->isEnabled()) {
                        throw new CustomUserMessageAuthenticationException(
                            'Invalid JSON Web Token: service token is disabled'
                        );
                    }
                    return true;
                },
                $jwt
            )
        );
        $passport->setAttribute('jwt', $jwt);
        $passport->setAttribute(JsonWebTokenManager::WRITEABLE_SCHOOLS_KEY, $schoolIds);
        return $passport;
    }
}
