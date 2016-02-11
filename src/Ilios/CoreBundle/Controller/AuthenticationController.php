<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Handler\AuthenticationHandler;
use Ilios\CoreBundle\Entity\AuthenticationInterface;

/**
 * Class AuthenticationController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("Authentications")
 */
class AuthenticationController extends FOSRestController
{
    /**
     * Create a Authentication.
     *
     * @ApiDoc(
     *   section = "Authentication",
     *   description = "Create an Authentication.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\AuthenticationType",
     *   output="Ilios\CoreBundle\Entity\Authentication",
     *   statusCodes={
     *     201 = "Created Authentication.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(statusCode=201, serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     *
     * @return Response
     */
    public function postAction(Request $request)
    {
        try {
            $handler = $this->getAuthenticationHandler();
            $data = $this->getPostData($request);

            if (!empty($data['password']) && !empty($data['user'])) {
                $userManager = $this->container->get('ilioscore.user.manager');
                $user = $userManager->findUserBy(['id' => $data['user']]);
                if ($user) {
                    $encoder = $this->container->get('security.password_encoder');
                    $encodedPassword = $encoder->encodePassword($user, $data['password']);
                    $data['passwordBcrypt'] = $encodedPassword;
                    unset($data['password']);
                }

            }

            $authentication = $handler->post($data);

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $authentication)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getAuthenticationHandler()->updateAuthentication($authentication, true, false);

            $answer['authentications'] = [$authentication];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update an Authentication.
     *
     * @ApiDoc(
     *   section = "Authentication",
     *   description = "Update a Authentication entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\AuthenticationType",
     *   output="Ilios\CoreBundle\Entity\Authentication",
     *   statusCodes={
     *     200 = "Updated Authentication.",
     *     201 = "Created Authentication.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $userId
     *
     * @return Response
     */
    public function putAction(Request $request, $userId)
    {
        try {
            $authentication = $this->getAuthenticationHandler()
                ->findAuthenticationBy(['user'=> $userId]);
            if ($authentication) {
                $code = Codes::HTTP_OK;
            } else {
                $authentication = $this->getAuthenticationHandler()
                    ->createAuthentication();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->getAuthenticationHandler();

            $data = $this->getPostData($request);

            if (!empty($data['password']) && !empty($data['user'])) {
                $userManager = $this->container->get('ilioscore.user.manager');
                $user = $userManager->findUserBy(['id' => $data['user']]);
                if ($user) {
                    $authentication->setPasswordSha256(null);
                    $encoder = $this->container->get('security.password_encoder');
                    $encodedPassword = $encoder->encodePassword($user, $data['password']);
                    $data['passwordBcrypt'] = $encodedPassword;
                    unset($data['password']);
                }

            }

            $authentication = $handler->put(
                $authentication,
                $data
            );

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $authentication)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $handler->updateAuthentication($authentication, true, true);

            $answer['authentication'] = $authentication;

        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }


    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return AuthenticationInterface $authentication
     */
    protected function getOr404($id)
    {
        $authentication = $this->getAuthenticationHandler()
            ->findAuthenticationBy(['id' => $id]);
        if (!$authentication) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $authentication;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('authentication')) {
            return $request->request->get('authentication');
        }

        return $request->request->all();
    }

    /**
     * @return AuthenticationHandler
     */
    protected function getAuthenticationHandler()
    {
        return $this->container->get('ilioscore.authentication.handler');
    }
}
