<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\AuthenticationRepository;
use App\Service\Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Traits\AuthenticationService;
use Symfony\Component\HttpFoundation\Response;

class LdapAuthentication implements AuthenticationInterface
{
    use AuthenticationService;

    protected ?string $ldapHost;

    protected ?string $ldapPort;

    protected ?string $ldapBindTemplate;

    public function __construct(
        protected AuthenticationRepository $authRepository,
        protected JsonWebTokenManager $jwtManager,
        Config $config,
        protected SessionUserProvider $sessionUserProvider
    ) {
        $this->ldapHost = $config->get('ldap_authentication_host');
        $this->ldapPort = $config->get('ldap_authentication_port');
        $this->ldapBindTemplate = $config->get('ldap_authentication_bind_template');
    }

    /**
     * Login a user using a username and password
     * to bind against an LDAP server
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
            $authEntity = $this->authRepository->findOneByUsername($username);
            if ($authEntity) {
                $sessionUser = $this->sessionUserProvider->createSessionUserFromUser($authEntity->getUser());
                if ($sessionUser->isEnabled()) {
                    $passwordValid = $this->checkLdapPassword($username, $password);
                    if ($passwordValid) {
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
     * Check against ldap to see if the user is valid
     */
    public function checkLdapPassword(string $username, string $password): bool
    {
        $ldapConn = @ldap_connect($this->ldapHost, $this->ldapPort);
        if ($ldapConn) {
            $ldapRdn = sprintf($this->ldapBindTemplate, $username);
            $ldapBind = @ldap_bind($ldapConn, $ldapRdn, $password);
            if ($ldapBind) {
                return true;
            }
        }

        return false;
    }

    public function getPublicConfigurationInformation(Request $request): array
    {
        $configuration = [];
        $configuration['type'] = 'ldap';

        return $configuration;
    }

    public function createAuthenticationResponse(Request $request): Response
    {
        return new Response();
    }
}
