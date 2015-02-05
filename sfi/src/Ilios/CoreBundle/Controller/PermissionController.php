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
use Ilios\CoreBundle\Handler\PermissionHandler;
use Ilios\CoreBundle\Entity\PermissionInterface;

/**
 * Permission controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("Permission")
 */
class PermissionController extends FOSRestController
{
    
    /**
     * Get a Permission
     *
     * @ApiDoc(
     *   description = "Get a Permission.",
     *   resource = true,
     *   requirements={
     *     {"name"="permissionId", "dataType"="integer", "requirement"="", "description"="Permission identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\Permission",
     *   statusCodes={
     *     200 = "Permission.",
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
        $answer['permission'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all Permission.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all Permission.",
     *   output="Ilios\CoreBundle\Entity\Permission",
     *   statusCodes = {
     *     200 = "List of all Permission",
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

        $answer['permission'] =
            $this->getPermissionHandler()->findPermissionsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['permission']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a Permission.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a Permission.",
     *   input="Ilios\CoreBundle\Form\PermissionType",
     *   output="Ilios\CoreBundle\Entity\Permission",
     *   statusCodes={
     *     201 = "Created Permission.",
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
            $new  =  $this->getPermissionHandler()->post($request->request->all());
            $answer['permission'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a Permission.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a Permission entity.",
     *   input="Ilios\CoreBundle\Form\PermissionType",
     *   output="Ilios\CoreBundle\Entity\Permission",
     *   statusCodes={
     *     200 = "Updated Permission.",
     *     201 = "Created Permission.",
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
            if ($permission = $this->getPermissionHandler()->findPermissionBy(['permissionId'=> $id])) {
                $answer['permission']= $this->getPermissionHandler()->put($permission, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['permission'] = $this->getPermissionHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a Permission.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a Permission.",
     *   input="Ilios\CoreBundle\Form\PermissionType",
     *   output="Ilios\CoreBundle\Entity\Permission",
     *   requirements={
     *     {"name"="permissionId", "dataType"="integer", "requirement"="", "description"="Permission identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated Permission.",
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
        $answer['permission'] = $this->getPermissionHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a Permission.
     *
     * @ApiDoc(
     *   description = "Delete a Permission entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "permissionId",
     *         "dataType" = "integer",
     *         "requirement" = "",
     *         "description" = "Permission identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted Permission.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal PermissionInterface $permission
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $permission = $this->getOr404($id);
        try {
            $this->getPermissionHandler()->deletePermission($permission);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return PermissionInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getPermissionHandler()->findPermissionBy(['permissionId' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $entity;
    }

    /**
     * @return PermissionHandler
     */
    public function getPermissionHandler()
    {
        return $this->container->get('ilioscore.permission.handler');
    }
}
