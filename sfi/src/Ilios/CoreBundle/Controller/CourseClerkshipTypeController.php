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
use Ilios\CoreBundle\Handler\CourseClerkshipTypeHandler;
use Ilios\CoreBundle\Entity\CourseClerkshipTypeInterface;

/**
 * CourseClerkshipType controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("CourseClerkshipType")
 */
class CourseClerkshipTypeController extends FOSRestController
{
    
    /**
     * Get a CourseClerkshipType
     *
     * @ApiDoc(
     *   description = "Get a CourseClerkshipType.",
     *   resource = true,
     *   requirements={
     *     {"name"="id", "dataType"="integer", "requirement"="", "description"="CourseClerkshipType identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\CourseClerkshipType",
     *   statusCodes={
     *     200 = "CourseClerkshipType.",
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
        $answer['courseClerkshipType'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all CourseClerkshipType.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all CourseClerkshipType.",
     *   output="Ilios\CoreBundle\Entity\CourseClerkshipType",
     *   statusCodes = {
     *     200 = "List of all CourseClerkshipType",
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

        $answer['courseClerkshipType'] =
            $this->getCourseClerkshipTypeHandler()->findCourseClerkshipTypesBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['courseClerkshipType']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a CourseClerkshipType.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a CourseClerkshipType.",
     *   input="Ilios\CoreBundle\Form\CourseClerkshipTypeType",
     *   output="Ilios\CoreBundle\Entity\CourseClerkshipType",
     *   statusCodes={
     *     201 = "Created CourseClerkshipType.",
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
            $new  =  $this->getCourseClerkshipTypeHandler()->post($request->request->all());
            $answer['courseClerkshipType'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a CourseClerkshipType.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a CourseClerkshipType entity.",
     *   input="Ilios\CoreBundle\Form\CourseClerkshipTypeType",
     *   output="Ilios\CoreBundle\Entity\CourseClerkshipType",
     *   statusCodes={
     *     200 = "Updated CourseClerkshipType.",
     *     201 = "Created CourseClerkshipType.",
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
            if ($courseClerkshipType = $this->getCourseClerkshipTypeHandler()->findCourseClerkshipTypeBy(['id'=> $id])) {
                $answer['courseClerkshipType']= $this->getCourseClerkshipTypeHandler()->put($courseClerkshipType, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['courseClerkshipType'] = $this->getCourseClerkshipTypeHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a CourseClerkshipType.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a CourseClerkshipType.",
     *   input="Ilios\CoreBundle\Form\CourseClerkshipTypeType",
     *   output="Ilios\CoreBundle\Entity\CourseClerkshipType",
     *   requirements={
     *     {"name"="id", "dataType"="integer", "requirement"="", "description"="CourseClerkshipType identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated CourseClerkshipType.",
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
        $answer['courseClerkshipType'] = $this->getCourseClerkshipTypeHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a CourseClerkshipType.
     *
     * @ApiDoc(
     *   description = "Delete a CourseClerkshipType entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "",
     *         "description" = "CourseClerkshipType identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted CourseClerkshipType.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal CourseClerkshipTypeInterface $courseClerkshipType
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $courseClerkshipType = $this->getOr404($id);
        try {
            $this->getCourseClerkshipTypeHandler()->deleteCourseClerkshipType($courseClerkshipType);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return CourseClerkshipTypeInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getCourseClerkshipTypeHandler()->findCourseClerkshipTypeBy(['id' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $entity;
    }

    /**
     * @return CourseClerkshipTypeHandler
     */
    public function getCourseClerkshipTypeHandler()
    {
        return $this->container->get('ilioscore.courseclerkshiptype.handler');
    }
}
