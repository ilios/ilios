<?php
namespace Ilios\AuthenticationBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Ilios\CoreBundle\Entity\UserInterface;

interface AuthenticationInterface
{
    /**
     * Login a user based on a Request and return a valid token or false on failure
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function login(Request $request);

    /**
     * Logout a user based on a Request and return some status
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function logout(Request $request);

    /**
     * Get public configuration information for this authentication type
     *
     * @param Request $request
     *
     * @return array
     */
    public function getPublicConfigurationInformation(Request $request);
}
