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
        $authenticator = $this->container->get('ilios_authentication.authenticator');
        
        return $authenticator->login($request);
    }
    
    /**
     * Get the id fro the currently authenticated user
     *
     * @return JsonResponse
     */
    public function whoamiAction()
    {
        $token = $this->get('security.token_storage')->getToken();
        if ($token->isAuthenticated()) {
            $user = $token->getUser();
            if ($user instanceof UserInterface) {
                return new JsonResponse(array('userId' => $user->getId()), JsonResponse::HTTP_OK);
            }
        }

        return new JsonResponse(array('userId' => null), JsonResponse::HTTP_OK);
    }
    
    /**
     * Get a new token
     * Useful when the time limit is approaching but the user is still active
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function tokenAction(Request $request)
    {
        $token = $this->get('security.token_storage')->getToken();
        if ($token->isAuthenticated()) {
            $user = $token->getUser();
            if ($user instanceof UserInterface) {
                $jwtManager = $this->container->get('ilios_authentication.jwt.manager');
                $ttl = $request->get('ttl')?$request->get('ttl'):'PT8H';
                $jwt = $jwtManager->createJwtFromUser($user, $ttl);
                return new JsonResponse(array('jwt' => $jwt), JsonResponse::HTTP_OK);
            }
        }

        return new JsonResponse(array('jwt' => null), JsonResponse::HTTP_OK);
    }

    /**
     * Logout
     * Passes off the task of logout to the service selected by the config
     * option authentication_type.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function logoutAction(Request $request)
    {
        $authenticator = $this->container->get('ilios_authentication.authenticator');

        return $authenticator->logout($request);
    }

    /**
     * Invalidate all tokens issued before now
     * Resets authentication in case a token is compromised
     *
     * @return JsonResponse
     */
    public function invalidateTokensAction()
    {
        $now = new \DateTime();
        $token = $this->get('security.token_storage')->getToken();
        if ($token->isAuthenticated()) {
            $user = $token->getUser();
            if ($user instanceof UserInterface) {
                $authentication = $user->getAuthentication();
                if (!$authentication) {
                    $authentication = $this->authenticationManager->create();
                    $authentication->setUser($user);
                }
                $authenticationManager = $this->container->get('ilioscore.authentication.manager');

                $authentication->setInvalidateTokenIssuedBefore($now);
                $authenticationManager->update($authentication);

                sleep(1);
                $jwtManager = $this->container->get('ilios_authentication.jwt.manager');
                $jwt = $jwtManager->createJwtFromUser($user);

                return new JsonResponse(array('jwt' => $jwt), JsonResponse::HTTP_OK);
            }
        }

        throw new \Exception('Attempted to invalidate token with no valid user');
    }
}
