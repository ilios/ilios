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
     * Handle any setup tasks on a new user
     * @param  array         $directoryInformation the stuff we get from LDAP
     * @param  UserInterface $user
     */
    public function setupNewUser(array $directoryInformation, UserInterface $user);
}
