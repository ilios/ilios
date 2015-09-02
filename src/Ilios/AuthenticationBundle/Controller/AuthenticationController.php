<?php

namespace Ilios\AuthenticationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\AuthenticationBundle\Jwt\Token as JwtToken;

class AuthenticationController extends Controller
{
    
    /**
     * Authenticate someone
     * Passes off the task of authentication to the service selected by the config
     * option authentication_type.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function loginAction(Request $request)
    {
        $authenticatorService = $this->container->getParameter('ilios_authentication.authenticatorservice');
        $authenticator = $this->container->get($authenticatorService);
        
        return $authenticator->login($request);

    }
    
    /**
     * Get the id fro the currently authenticated user
     *
     * @return JsonResponse
     */
    public function whoamiAction()
    {
        $token = $this->get('security.context')->getToken();
        if ($token instanceof JwtToken) {
            $userId = $this->get('security.context')->getToken()->getUserId();
            return new JsonResponse(array('userId' => $userId), JsonResponse::HTTP_OK);
        }

        return new JsonResponse(array('userId' => null), JsonResponse::HTTP_OK);
    }
    
    /**
     * Refresh the current token
     * Useful when the time limit is approaching but the user is still active
     *
     * @return JsonResponse
     */
    public function refreshAction()
    {
        $token = $this->get('security.context')->getToken();
        if ($token) {
            $user = $token->getUser();
            if ($user instanceof UserInterface) {
                return new JsonResponse(array('jwt' => $token->getJwt()), JsonResponse::HTTP_OK);
            }
        }

        return new JsonResponse(array('jwt' => null), JsonResponse::HTTP_OK);
    }
}
