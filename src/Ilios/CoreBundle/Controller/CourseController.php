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
use Ilios\CoreBundle\Handler\CourseHandler;
use Ilios\CoreBundle\Entity\CourseInterface;

/**
 * Course controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("Course")
 */
class CourseController extends FOSRestController
{
    
    /**
     * Get a Course
     *
     * @ApiDoc(
     *   description = "Get a Course.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="",
     *        "description"="Course identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\Course",
     *   statusCodes={
     *     200 = "Course.",
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
        $answer['course'] = $this->getOr404($id);

        return $answer;
    }
    /**
     * Get all Course.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all Course.",
     *   output="Ilios\CoreBundle\Entity\Course",
     *   statusCodes = {
     *     200 = "List of all Course",
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

        $criteria = array_map(function ($item) {
            $item = $item == 'null'?null:$item;
            $item = $item == 'false'?false:$item;
            $item = $item == 'true'?true:$item;
            return $item;
        }, $criteria);

        $result = $this->getCourseHandler()
            ->findCoursesBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );
        //If there are no matches return an empty array
        $answer['courses'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a Course.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a Course.",
     *   input="Ilios\CoreBundle\Form\CourseType",
     *   output="Ilios\CoreBundle\Entity\Course",
     *   statusCodes={
     *     201 = "Created Course.",
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
            $new  =  $this->getCourseHandler()->post($this->getPostData($request));
            $answer['course'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a Course.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a Course entity.",
     *   input="Ilios\CoreBundle\Form\CourseType",
     *   output="Ilios\CoreBundle\Entity\Course",
     *   statusCodes={
     *     200 = "Updated Course.",
     *     201 = "Created Course.",
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
            $course = $this->getCourseHandler()
                ->findCourseBy(['id'=> $id]);
            if ($course) {
                $answer['course'] =
                    $this->getCourseHandler()->put(
                        $course,
                        $this->getPostData($request)
                    );
                $code = Codes::HTTP_OK;
            } else {
                $answer['course'] =
                    $this->getCourseHandler()->post($this->getPostData($request));
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a Course.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a Course.",
     *   input="Ilios\CoreBundle\Form\CourseType",
     *   output="Ilios\CoreBundle\Entity\Course",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="",
     *         "description"="Course identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated Course.",
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
        $answer['course'] =
            $this->getCourseHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a Course.
     *
     * @ApiDoc(
     *   description = "Delete a Course entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "",
     *         "description" = "Course identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted Course.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal CourseInterface $course
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $course = $this->getOr404($id);
        try {
            $this->getCourseHandler()
                ->deleteCourse($course);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return CourseInterface $entity
     */
    protected function getOr404($id)
    {
        $entity = $this->getCourseHandler()
            ->findCourseBy(['id' => $id]);
        if (!$entity) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $entity;
    }
   /**
    * Parse the request for the form data
    *
    * @param Request $request
    * @return array
     */
    protected function getPostData(Request $request)
    {
        return $request->request->get('course', array());
    }
    /**
     * @return CourseHandler
     */
    protected function getCourseHandler()
    {
        return $this->container->get('ilioscore.course.handler');
    }
}
