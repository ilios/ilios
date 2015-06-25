<?php

namespace Ilios\AuthenticationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Ilios\AuthenticationBundle\Jwt\Token as JwtToken;

class AuthenticationController extends Controller
{
    public function loginAction(Request $request)
    {
        $type = $this->container->getParameter('ilios_authentication.type');
        $username = $request->request->get('username');
        $password = $request->request->get('password');
        $errors = [];
        if (!$username) {
            $errors[] = 'Username is required';
        }
        if (!$password) {
            $errors[] = 'Password is required';
        }

        if (empty($errors) && $type === 'form') {
            $authManager = $this->container->get('ilioscore.authentication.manager');
            $authEntity = $authManager->findAuthenticationBy(array('username' => $username));
            $user = $authEntity->getUser();
            $encoder = $this->container->get('security.password_encoder');
            $jwtKey = $this->container->getParameter('kernel.secret');
            $passwordValid = $encoder->isPasswordValid($user, $password);
            if ($passwordValid) {
                $token = new JwtToken($jwtKey);
                $token->setUser($user);

                return new JsonResponse(array('jwt' => $token->getJwt()), JsonResponse::HTTP_OK);
            }
            $errors[] = 'Incorrect username or password';

        }

        return new JsonResponse(array('errors' => $errors), JsonResponse::HTTP_BAD_REQUEST);

    }
    public function logoutAction(Request $request)
    {
        $type = $this->container->getParameter('ilios_authentication.type');
        die('logging out');
        return new JsonResponse(array('config' => $configuration));
    }
}
