<?php

namespace Ilios\AuthenticationBundle\Controller;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\AuthenticationBundle\Service\JsonWebTokenManager;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

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
            /** @var SessionUserInterface $sessionUser */
            $sessionUser = $token->getUser();
            if ($sessionUser instanceof SessionUserInterface) {
                return new JsonResponse(array('userId' => $sessionUser->getId()), JsonResponse::HTTP_OK);
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
            $sessionUser = $token->getUser();
            if ($sessionUser instanceof SessionUserInterface) {
                /** @var JsonWebTokenManager $jwtManager */
                $jwtManager = $this->container->get('ilios_authentication.jwt.manager');
                $ttl = $request->get('ttl')?$request->get('ttl'):'PT8H';
                $jwt = $jwtManager->createJwtFromUser($sessionUser, $ttl);
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
            /** @var SessionUserInterface $sessionUser */
            $sessionUser = $token->getUser();
            if ($sessionUser instanceof SessionUserInterface) {
                $userManager = $this->container->get('ilioscore.user.manager');
                /** @var UserInterface $user */
                $user = $userManager->findOneBy(['id' => $sessionUser->getId()]);
                $authenticationManager = $this->container->get('ilioscore.authentication.manager');
                $authentication = $authenticationManager->findOneBy(['user' => $user->getId()]);
                if (!$authentication) {
                    $authentication = $authenticationManager->create();
                    $authentication->setUser($user);
                }

                $authentication->setInvalidateTokenIssuedBefore($now);
                $authenticationManager->update($authentication);

                sleep(1);
                $jwtManager = $this->container->get('ilios_authentication.jwt.manager');
                $jwt = $jwtManager->createJwtFromUser($sessionUser);

                return new JsonResponse(array('jwt' => $jwt), JsonResponse::HTTP_OK);
            }
        }

        throw new \Exception('Attempted to invalidate token with no valid user');
    }
}
