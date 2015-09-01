<?php

namespace Ilios\AuthenticationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\AuthenticationBundle\Jwt\Token as JwtToken;

class AuthenticationController extends Controller
{
    
    public function loginAction(Request $request)
    {
        $authenticatorService = $this->container->getParameter('ilios_authentication.authenticatorservice');
        $authenticator = $this->container->get($authenticatorService);
        
        return $authenticator->login($request);

    }
    
    public function whoamiAction()
    {
        $token = $this->get('security.context')->getToken();
        if ($token instanceof JwtToken) {
            $userId = $this->get('security.context')->getToken()->getUserId();
            return new JsonResponse(array('userId' => $userId), JsonResponse::HTTP_OK);
        }

        return new JsonResponse(array('userId' => null), JsonResponse::HTTP_OK);
    }
    
    public function logoutAction(Request $request)
    {
        $this->get('security.context')->setToken(null);

        return new JsonResponse(array('logout' => true));
    }
    
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
