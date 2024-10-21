<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\AuthenticationRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Traits\AuthenticationService;
use App\Entity\AuthenticationInterface as AuthenticationEntityInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ShibbolethAuthentication
 */
class ShibbolethAuthentication implements AuthenticationInterface
{
    use AuthenticationService;

    protected ?string $logoutPath;

    protected ?string $loginPath;

    protected ?string $userIdAttribute;

    /**
     * Constructor
     */
    public function __construct(
        protected AuthenticationRepository $authenticationRepository,
        protected JsonWebTokenManager $jwtManager,
        protected LoggerInterface $logger,
        Config $config,
        protected SessionUserProvider $sessionUserProvider
    ) {
        $this->logoutPath = $config->get('shibboleth_authentication_logout_path');
        $this->loginPath = $config->get('shibboleth_authentication_login_path');
        $this->userIdAttribute = $config->get('shibboleth_authentication_user_id_attribute');
    }

    /**
     * Authenticate a user from shibboleth
     *
     * If the user is not yet logged in send a redirect Request
     * If the user is logged in, but no account exists send an error
     * If the user is authenticated send a JWT
     *
     * @throws Exception when the shibboleth attributes do not contain a value for the configured user id attribute
     */
    public function login(Request $request): JsonResponse
    {
        $applicationId = $request->server->get('Shib-Application-ID');
        if (!$applicationId) {
            return new JsonResponse([
                'status' => 'redirect',
                'errors' => [],
                'jwt' => null,
            ], JsonResponse::HTTP_OK);
        }
        $userId = $request->server->get($this->userIdAttribute);
        if (!$userId) {
            $msg =  "No '{$this->userIdAttribute}' found for authenticated user.";
            $logVars = [];

            $shibProperties = [
                'Shib-Session-ID',
                'Shib-Authentication-Instant',
                'Shib-Authentication-Method',
                'Shib-Session-Index',
            ];
            foreach ($shibProperties as $key) {
                $logVars[$key] = $request->server->get($key);
            }

            $logVars['HTTP_REFERER'] = $request->server->get('HTTP_REFERER');
            $logVars['REMOTE_ADDR'] = $request->server->get('REMOTE_ADDR');

            $this->logger->info($msg, ['server variables' => var_export($logVars, true)]);

            return new JsonResponse([
                'status' => 'redirect',
                'errors' => [],
                'jwt' => null,
            ], JsonResponse::HTTP_OK);
        }
        /** @var ?AuthenticationEntityInterface $authEntity */
        $authEntity = $this->authenticationRepository->findOneBy(['username' => $userId]);
        if ($authEntity) {
            $sessionUser = $this->sessionUserProvider->createSessionUserFromUser($authEntity->getUser());
            if ($sessionUser->isEnabled()) {
                $jwt = $this->jwtManager->createJwtFromSessionUser($sessionUser);

                return $this->createSuccessResponseFromJWT($jwt);
            }
        }

        return new JsonResponse([
            'status' => 'noAccountExists',
            'userId' => $userId,
            'errors' => [],
            'jwt' => null,
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Logout a user
     */
    public function logout(Request $request): JsonResponse
    {
        $url = $request->getSchemeAndHttpHost();
        $logoutUrl = $url . $this->logoutPath;
        if (session_status() !== PHP_SESSION_NONE) {
            session_destroy();
        }
        return new JsonResponse([
            'status' => 'redirect',
            'logoutUrl' => $logoutUrl,

        ], JsonResponse::HTTP_OK);
    }

    public function getPublicConfigurationInformation(Request $request): array
    {
        $configuration = [];
        $configuration['type'] = 'shibboleth';
        $url = $request->getSchemeAndHttpHost();
        $configuration['loginUrl'] = $url . $this->loginPath;

        return $configuration;
    }

    public function createAuthenticationResponse(Request $request): Response
    {
        $applicationId = $request->server->get('Shib-Application-ID');
        if (!$applicationId) {
            $configuration = $this->getPublicConfigurationInformation($request);
            $url = $configuration['loginUrl'] . "?target=" . $request->getRequestUri();
            return new RedirectResponse($url);
        }

        return new Response();
    }
}
