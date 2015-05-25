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
use Ilios\CoreBundle\Handler\UserHandler;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class UserController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("Users")
 */
class UserController extends FOSRestController
{
    /**
     * Get a User
     *
     * @ApiDoc(
     *   section = "User",
     *   description = "Get a User.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="User identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\User",
     *   statusCodes={
     *     200 = "User.",
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
    public function getAction($id)
    {
        $answer['user'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all User.
     *
     * @ApiDoc(
     *   section = "User",
     *   description = "Get all User.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\User",
     *   statusCodes = {
     *     200 = "List of all User",
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

        $result = $this->getUserHandler()
            ->findUsersBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        //If there are no matches return an empty array
        $answer['users'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a User.
     *
     * @ApiDoc(
     *   section = "User",
     *   description = "Create a User.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\UserType",
     *   output="Ilios\CoreBundle\Entity\User",
     *   statusCodes={
     *     201 = "Created User.",
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
            $user = $this->getUserHandler()
                ->post($this->getPostData($request));

            $response = new Response();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set(
                'Location',
                $this->generateUrl(
                    'get_users',
                    ['id' => $user->getId()],
                    true
                )
            );

            return $response;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a User.
     *
     * @ApiDoc(
     *   section = "User",
     *   description = "Update a User entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\UserType",
     *   output="Ilios\CoreBundle\Entity\User",
     *   statusCodes={
     *     200 = "Updated User.",
     *     201 = "Created User.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $id
     *
     * @return Response
     */
    public function putAction(Request $request, $id)
    {
        try {
            $user = $this->getUserHandler()
                ->findUserBy(['id'=> $id]);
            if ($user) {
                $code = Codes::HTTP_OK;
            } else {
                $user = $this->getUserHandler()->createUser();
                $code = Codes::HTTP_CREATED;
            }

            $answer['user'] =
                $this->getUserHandler()->put(
                    $user,
                    $this->getPostData($request)
                );
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a User.
     *
     * @ApiDoc(
     *   section = "User",
     *   description = "Partial Update to a User.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\UserType",
     *   output="Ilios\CoreBundle\Entity\User",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="\d+",
     *         "description"="User identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated User.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $id
     *
     * @return Response
     */
    public function patchAction(Request $request, $id)
    {
        $answer['user'] =
            $this->getUserHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a User.
     *
     * @ApiDoc(
     *   section = "User",
     *   description = "Delete a User entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "User identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted User.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal UserInterface $user
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $user = $this->getOr404($id);

        try {
            $this->getUserHandler()->deleteUser($user);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return UserInterface $user
     */
    protected function getOr404($id)
    {
        $user = $this->getUserHandler()
            ->findUserBy(['id' => $id]);
        if (!$user) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $user;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('user');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return UserHandler
     */
    protected function getUserHandler()
    {
        return $this->container->get('ilioscore.user.handler');
    }
}
