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
use Ilios\CoreBundle\Handler\UserRoleHandler;
use Ilios\CoreBundle\Entity\UserRoleInterface;

/**
 * Class UserRoleController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("UserRoles")
 */
class UserRoleController extends FOSRestController
{
    /**
     * Get a UserRole
     *
     * @ApiDoc(
     *   section = "UserRole",
     *   description = "Get a UserRole.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="UserRole identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\UserRole",
     *   statusCodes={
     *     200 = "UserRole.",
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
        $answer['userRole'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all UserRole.
     *
     * @ApiDoc(
     *   section = "UserRole",
     *   description = "Get all UserRole.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\UserRole",
     *   statusCodes = {
     *     200 = "List of all UserRole",
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

        $result = $this->getUserRoleHandler()
            ->findUserRolesBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        //If there are no matches return an empty array
        $answer['userRoles'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a UserRole.
     *
     * @ApiDoc(
     *   section = "UserRole",
     *   description = "Create a UserRole.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\UserRoleType",
     *   output="Ilios\CoreBundle\Entity\UserRole",
     *   statusCodes={
     *     201 = "Created UserRole.",
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
            $userrole = $this->getUserRoleHandler()
                ->post($this->getPostData($request));

            $response = new Response();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set(
                'Location',
                $this->generateUrl(
                    'get_userroles',
                    ['id' => $userrole->getId()],
                    true
                )
            );

            return $response;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a UserRole.
     *
     * @ApiDoc(
     *   section = "UserRole",
     *   description = "Update a UserRole entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\UserRoleType",
     *   output="Ilios\CoreBundle\Entity\UserRole",
     *   statusCodes={
     *     200 = "Updated UserRole.",
     *     201 = "Created UserRole.",
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
            $userRole = $this->getUserRoleHandler()
                ->findUserRoleBy(['id'=> $id]);
            if ($userRole) {
                $code = Codes::HTTP_OK;
            } else {
                $userRole = $this->getUserRoleHandler()->createUserRole();
                $code = Codes::HTTP_CREATED;
            }

            $answer['userRole'] =
                $this->getUserRoleHandler()->put(
                    $userRole,
                    $this->getPostData($request)
                );
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a UserRole.
     *
     * @ApiDoc(
     *   section = "UserRole",
     *   description = "Partial Update to a UserRole.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\UserRoleType",
     *   output="Ilios\CoreBundle\Entity\UserRole",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="\d+",
     *         "description"="UserRole identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated UserRole.",
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
        $answer['userRole'] =
            $this->getUserRoleHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a UserRole.
     *
     * @ApiDoc(
     *   section = "UserRole",
     *   description = "Delete a UserRole entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "UserRole identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted UserRole.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal UserRoleInterface $userRole
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $userRole = $this->getOr404($id);

        try {
            $this->getUserRoleHandler()->deleteUserRole($userRole);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return UserRoleInterface $userRole
     */
    protected function getOr404($id)
    {
        $userRole = $this->getUserRoleHandler()
            ->findUserRoleBy(['id' => $id]);
        if (!$userRole) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $userRole;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('userRole');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return UserRoleHandler
     */
    protected function getUserRoleHandler()
    {
        return $this->container->get('ilioscore.userrole.handler');
    }
}
