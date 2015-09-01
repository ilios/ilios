<?php
namespace Ilios\AuthenticationBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

interface AuthenticationInterface
{
    /**
     * Login a user based on a Request and return a valid token or false on failure
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function login(Request $request);
}
