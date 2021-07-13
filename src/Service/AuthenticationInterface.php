<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\UserInterface;

interface AuthenticationInterface
{
    /**
     * Login a user based on a Request and return a valid token or false on failure
     *
     * @return JsonResponse
     */
    public function login(Request $request);

    /**
     * Logout a user based on a Request and return some status
     *
     * @return JsonResponse
     */
    public function logout(Request $request);

    /**
     * Get public configuration information for this authentication type
     *
     *
     * @return array
     */
    public function getPublicConfigurationInformation(Request $request);

    /**
     * Attempt to authenticate the user and send either an empty Response
     * or a redirect response for SSO auth.
     *
     *
     */
    public function createAuthenticationResponse(Request $request): Response;
}
