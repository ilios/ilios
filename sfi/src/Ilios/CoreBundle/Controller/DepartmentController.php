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
use Ilios\CoreBundle\Handler\DepartmentHandler;
use Ilios\CoreBundle\Entity\DepartmentInterface;

/**
 * Department controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("Department")
 */
class DepartmentController extends FOSRestController
{
    
    /**
     * Get a Department
     *
     * @ApiDoc(
     *   description = "Get a Department.",
     *   resource = true,
     *   requirements={
     *     {"name"="departmentId", "dataType"="integer", "requirement"="", "description"="Department identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\Department",
     *   statusCodes={
     *     200 = "Department.",
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
        $answer['department'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all Department.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all Department.",
     *   output="Ilios\CoreBundle\Entity\Department",
     *   statusCodes = {
     *     200 = "List of all Department",
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

        $answer['department'] =
            $this->getDepartmentHandler()->findDepartmentsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['department']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a Department.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a Department.",
     *   input="Ilios\CoreBundle\Form\DepartmentType",
     *   output="Ilios\CoreBundle\Entity\Department",
     *   statusCodes={
     *     201 = "Created Department.",
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
            $new  =  $this->getDepartmentHandler()->post($request->request->all());
            $answer['department'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a Department.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a Department entity.",
     *   input="Ilios\CoreBundle\Form\DepartmentType",
     *   output="Ilios\CoreBundle\Entity\Department",
     *   statusCodes={
     *     200 = "Updated Department.",
     *     201 = "Created Department.",
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
            if ($department = $this->getDepartmentHandler()->findDepartmentBy(['departmentId'=> $id])) {
                $answer['department']= $this->getDepartmentHandler()->put($department, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['department'] = $this->getDepartmentHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a Department.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a Department.",
     *   input="Ilios\CoreBundle\Form\DepartmentType",
     *   output="Ilios\CoreBundle\Entity\Department",
     *   requirements={
     *     {"name"="departmentId", "dataType"="integer", "requirement"="", "description"="Department identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated Department.",
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
        $answer['department'] = $this->getDepartmentHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a Department.
     *
     * @ApiDoc(
     *   description = "Delete a Department entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "departmentId",
     *         "dataType" = "integer",
     *         "requirement" = "",
     *         "description" = "Department identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted Department.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal DepartmentInterface $department
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $department = $this->getOr404($id);
        try {
            $this->getDepartmentHandler()->deleteDepartment($department);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return DepartmentInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getDepartmentHandler()->findDepartmentBy(['departmentId' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $entity;
    }

    /**
     * @return DepartmentHandler
     */
    public function getDepartmentHandler()
    {
        return $this->container->get('ilioscore.department.handler');
    }
}
