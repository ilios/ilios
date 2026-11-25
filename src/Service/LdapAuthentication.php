<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\AuthenticationRepository;
use App\Service\Jwt\TokenCodec;
use App\Service\Jwt\TokenManager;
use App\Traits\AuthenticationService;
use Override;
use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LdapAuthentication implements AuthenticationInterface
{
    use AuthenticationService;

    protected ?string $ldapHost;

    protected ?string $ldapPort;

    protected ?string $ldapBindTemplate;

    public function __construct(
        protected AuthenticationRepository $authRepository,
        protected TokenCodec $tokenCodec,
        protected TokenManager $tokenManager,
        protected Config $config,
        protected SessionUserProvider $sessionUserProvider
    ) {
    }

    /**
     * @psalm-taint-escape ssrf
     */
    protected function getLdapHost(): string
    {
        if (!isset($this->ldapHost)) {
            $host = $this->config->get('ldap_authentication_host');
            if (!isset($host)) {
                throw new RuntimeException('LDAP host must be set.');
            }
            $this->ldapHost = $host;
        }
        return $this->ldapHost;
    }

    /**
     * @psalm-taint-escape ssrf
     */
    protected function getLdapPort(): string
    {
        if (!isset($this->ldapPort)) {
            $port = $this->config->get('ldap_authentication_port');
            if (!isset($port)) {
                throw new RuntimeException('LDAP port must be set.');
            }
            $this->ldapPort = $port;
        }
        return $this->ldapPort;
    }

    /**
     * @psalm-taint-escape ssrf
     */
    protected function getLdapBindTemplate(): string
    {
        if (!isset($this->ldapBindTemplate)) {
            $template = $this->config->get('ldap_authentication_bind_template');
            if (!isset($template)) {
                throw new RuntimeException('LDAP bind template must be set.');
            }
            $this->ldapBindTemplate = $template;
        }
        return $this->ldapBindTemplate;
    }

    /**
     * Login a user using a username and password
     * to bind against an LDAP server
     */
    #[Override]
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
            $authEntity = $this->authRepository->findOneByUsername($username);
            if ($authEntity) {
                $sessionUser = $this->sessionUserProvider->createSessionUserFromUser($authEntity->getUser());
                if ($sessionUser->isEnabled()) {
                    $passwordValid = $this->checkLdapPassword($username, $password);
                    if ($passwordValid) {
                        $userToken = $this->tokenManager->createUserTokenForSessionUser($sessionUser);
                        $jwt = $this->tokenCodec->encode($userToken);
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
    #[Override]
    public function logout(Request $request): JsonResponse
    {
        return new JsonResponse([
            'status' => 'success',
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Check against ldap to see if the user is valid
     */
    public function checkLdapPassword(string $username, string $password): bool
    {
        $ldapConn = @ldap_connect("{$this->getLdapHost()}:{$this->getLdapPort()}");
        if ($ldapConn) {
            $ldapRdn = sprintf($this->getLdapBindTemplate(), $username);
            $ldapBind = @ldap_bind($ldapConn, $ldapRdn, $password);
            if ($ldapBind) {
                return true;
            }
        }

        return false;
    }

    #[Override]
    public function getPublicConfigurationInformation(Request $request): array
    {
        $configuration = [];
        $configuration['type'] = 'ldap';

        return $configuration;
    }

    #[Override]
    public function createAuthenticationResponse(Request $request): Response
    {
        return new Response();
    }
}
