<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Ilios\CoreBundle\Entity\AuthenticationInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\RestBundle\Request\ParamFetcherInterface;

/**
 * Class AuthenticationController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("Authentications")
 */
class AuthenticationController extends FOSRestController
{
    /**
     * Get a Authentication
     *
     * @ApiDoc(
     *   section = "Authentication",
     *   description = "Get an Authentication.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="Authentication identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\Authentication",
     *   statusCodes={
     *     200 = "Authentication.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return Response
     */
    public function getAction($user)
    {
        $manager = $this->container->get('ilioscore.authentication.manager');
        $authentication = $manager->findDTOBy(['user' => $user]);

        if (!$authentication) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $user));
        }

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $authentication)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['authentications'][] = $authentication;

        return $answer;
    }

    /**
     * Get all Authentication.
     *
     * @ApiDoc(
     *   section = "Authentication",
     *   description = "Get all Authentication.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\Authentication",
     *   statusCodes = {
     *     200 = "List of all Authentication",
     *     204 = "No content. Nothing to list."
     *   }
     * )
     *
     * @QueryParam(
     *   name="offset",
     *   requirements="\d+",
     *   nullable=true,
     *   description="Offset from which to start listing notes."
     * )
     * @QueryParam(
     *   name="limit",
     *   requirements="\d+",
     *   default="20",
     *   description="How many notes to return."
     * )
     * @QueryParam(
     *   name="order_by",
     *   nullable=true,
     *   array=true,
     *   description="Order by fields. Must be an array ie. &order_by[name]=ASC&order_by[description]=DESC"
     * )
     * @QueryParam(
     *   name="filters",
     *   nullable=true,
     *   array=true,
     *   description="Filter by fields. Must be an array ie. &filters[id]=3"
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return Response
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        $orderBy = $paramFetcher->get('order_by');
        $criteria = !is_null($paramFetcher->get('filters')) ? $paramFetcher->get('filters') : [];
        $criteria = array_map(function ($item) {
            $item = $item == 'null' ? null : $item;
            $item = $item == 'false' ? false : $item;
            $item = $item == 'true' ? true : $item;

            return $item;
        }, $criteria);

        $manager = $this->container->get('ilioscore.authentication.manager');
        $result = $manager->findDTOsBy($criteria, $orderBy, $limit, $offset);

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['authentications'] = $result ? array_values($result) : [];

        return $answer;
    }
    
    /**
     * Create a Authentication.
     *
     * @ApiDoc(
     *   section = "Authentication",
     *   description = "Create an Authentication or multiple authentications (max 500).",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\AuthenticationType",
     *   output="Ilios\CoreBundle\Entity\Authentication",
     *   statusCodes={
     *     201 = "Created Authentication.",
     *     400 = "Bad Request or too many authentications submitted.",
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
            $handler = $this->container->get('ilioscore.authentication.handler');
            $arr = $this->getPostData($request);
            $count = count($arr);
            if ($count > 500) {
                throw new BadRequestHttpException("Maximum of 500 users can be created.  You sent " . $count);
            }

            $unsavedItems = [];
            $manager = $this->container->get('ilioscore.authentication.manager');
            $encoder = $this->container->get('security.password_encoder');
            $userManager = $this->container->get('ilioscore.user.manager');

            $needingHashedPassword = array_filter($arr, function ($arr) {
                return (!empty($arr['password']) && !empty($arr['user']));
            });
            $userIdsForHashing = array_map(function ($arr) {
                return $arr['user'];
            }, $needingHashedPassword);
            //prefetch all the users we need for hashing
            $users = [];
            foreach ($userManager->findBy(['id' => $userIdsForHashing]) as $user) {
                $users[$user->getId()] = $user;
            }

            foreach ($arr as $data) {
                if (!empty($data['password']) && !empty($data['user'])) {
                    $user = $users[$data['user']];
                    if ($user) {
                        $encodedPassword = $encoder->encodePassword($user, $data['password']);
                        $data['passwordBcrypt'] = $encodedPassword;
                    }
                }
                //unset the password here in case it is NULL and didn't satisfy the above condition
                unset($data['password']);

                $authentication = $handler->post($data);

                $authChecker = $this->get('security.authorization_checker');
                if (! $authChecker->isGranted('create', $authentication)) {
                    throw $this->createAccessDeniedException('Unauthorized access!');
                }

                $manager->update($authentication, false, false);

                $unsavedItems[] = $authentication;
            }

            $manager->flush();

            $ids = array_map(function (AuthenticationInterface $authentication) {
                return $authentication->getUser()->getId();
            }, $unsavedItems);
            unset($unsavedUsers);

            $newItems = $manager->findDTOsBy(['user' => $ids]);

            $answer['authentications'] = $newItems;

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
            $manager = $this->container->get('ilioscore.authentication.manager');
            $authentication = $manager->findOneBy(['user'=> $userId]);
            if ($authentication) {
                $code = Codes::HTTP_OK;
            } else {
                $authentication = $manager->create();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->container->get('ilioscore.authentication.handler');
            $data = $this->getPostData($request)[0];

            if (!empty($data['password']) && !empty($data['user'])) {
                $userManager = $this->container->get('ilioscore.user.manager');
                $user = $userManager->findOneBy(['id' => $data['user']]);
                if ($user) {
                    $authentication->setPasswordSha256(null);
                    $encoder = $this->container->get('security.password_encoder');
                    $encodedPassword = $encoder->encodePassword($user, $data['password']);
                    $data['passwordBcrypt'] = $encodedPassword;
                    unset($data['password']);
                }
            }

            $authentication = $handler->put($authentication, $data);

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $authentication)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager->update($authentication, true, true);

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
        $manager = $this->container->get('ilioscore.authentication.manager');
        $authentication = $manager->findOneBy(['id' => $id]);
        if (!$authentication) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $authentication;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array of authentication form data
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('authentication')) {
            return [$request->request->get('authentication')];
        }
        //multiple authentication can be created in the same request
        if ($request->request->has('authentications')) {
            return $request->request->get('authentications');
        }

        return [$request->request->all()];
    }
}
