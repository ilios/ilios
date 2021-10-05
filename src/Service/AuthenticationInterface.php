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
     */
    public function login(Request $request): Response;

    /**
     * Logout a user based on a Request and return some status
     */
    public function logout(Request $request): JsonResponse;

    /**
     * Get public configuration information for this authentication type
     */
    public function getPublicConfigurationInformation(Request $request): array;

    /**
     * Attempt to authenticate the user and send either an empty Response
     * or a redirect response for SSO auth.
     */
    public function createAuthenticationResponse(Request $request): Response;
}
