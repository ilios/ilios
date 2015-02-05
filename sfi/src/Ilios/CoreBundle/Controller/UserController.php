<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View as FOSView;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Handler\UserHandler;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * User controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("User")
 */
class UserController extends FOSRestController
{
    
    /**
     * Get a User
     *
     * @ApiDoc(
     *   description = "Get a User.",
     *   resource = true,
     *   requirements={
     *     {"name"="id", "dataType"="integer", "requirement"="", "description"="User identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\User",
     *   statusCodes={
     *     200 = "User.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $id
     *
     * @return Response
     */
    public function getAction(Request $request, $id)
    {
        $answer['user'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all User.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all User.",
     *   output="Ilios\CoreBundle\Entity\User",
     *   statusCodes = {
     *     200 = "List of all User",
     *     204 = "No content. Nothing to list."
     *   }
     * )
     *
     * @View(serializerEnableMaxDepthChecks=true)
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return Response
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
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        $orderBy = $paramFetcher->get('order_by');
        $criteria = !is_null($paramFetcher->get('filters')) ? $paramFetcher->get('filters') : array();

        $answer['user'] =
            $this->getUserHandler()->findUsersBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['user']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a User.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a User.",
     *   input="Ilios\CoreBundle\Form\UserType",
     *   output="Ilios\CoreBundle\Entity\User",
     *   statusCodes={
     *     201 = "Created User.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @View(statusCode=201, serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     *
     * @return Response
     */
    public function postAction(Request $request)
    {
        try {
            $new  =  $this->getUserHandler()->post($request->request->all());
            $answer['user'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a User.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a User entity.",
     *   input="Ilios\CoreBundle\Form\UserType",
     *   output="Ilios\CoreBundle\Entity\User",
     *   statusCodes={
     *     200 = "Updated User.",
     *     201 = "Created User.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $entity
     *
     * @return Response
     */
    public function putAction(Request $request, $id)
    {
        try {
            if ($user = $this->getUserHandler()->findUserBy(['id'=> $id])) {
                $answer['user']= $this->getUserHandler()->put($user, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['user'] = $this->getUserHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
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
     *   resource = true,
     *   description = "Partial Update to a User.",
     *   input="Ilios\CoreBundle\Form\UserType",
     *   output="Ilios\CoreBundle\Entity\User",
     *   requirements={
     *     {"name"="id", "dataType"="integer", "requirement"="", "description"="User identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated User.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     *
     * @View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $entity
     *
     * @return Response
     */
    public function patchAction(Request $request, $id)
    {
        $answer['user'] = $this->getUserHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a User.
     *
     * @ApiDoc(
     *   description = "Delete a User entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "",
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
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal UserInterface $user
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
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
     * @return UserInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getUserHandler()->findUserBy(['id' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $entity;
    }

    /**
     * @return UserHandler
     */
    public function getUserHandler()
    {
        return $this->container->get('ilioscore.user.handler');
    }
}
