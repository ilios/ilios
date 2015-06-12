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
use Ilios\CoreBundle\Handler\CourseHandler;
use Ilios\CoreBundle\Entity\CourseInterface;

/**
 * Class CourseController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("Courses")
 */
class CourseController extends FOSRestController
{
    /**
     * Get a Course
     *
     * @ApiDoc(
     *   section = "Course",
     *   description = "Get a Course.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
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
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return Response
     */
    public function getAction($id)
    {
        $answer['courses'][] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all Course.
     *
     * @ApiDoc(
     *   section = "Course",
     *   description = "Get all Course.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\Course",
     *   statusCodes = {
     *     200 = "List of all Course",
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
     *   section = "Course",
     *   description = "Create a Course.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CourseType",
     *   output="Ilios\CoreBundle\Entity\Course",
     *   statusCodes={
     *     201 = "Created Course.",
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
            $course = $this->getCourseHandler()
                ->post($this->getPostData($request));

            $response = new Response();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set(
                'Location',
                $this->generateUrl(
                    'get_courses',
                    ['id' => $course->getId()],
                    true
                )
            );

            return $response;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a Course.
     *
     * @ApiDoc(
     *   section = "Course",
     *   description = "Update a Course entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CourseType",
     *   output="Ilios\CoreBundle\Entity\Course",
     *   statusCodes={
     *     200 = "Updated Course.",
     *     201 = "Created Course.",
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
            $course = $this->getCourseHandler()
                ->findCourseBy(['id'=> $id]);
            if ($course) {
                $code = Codes::HTTP_OK;
            } else {
                $course = $this->getCourseHandler()
                    ->createCourse();
                $code = Codes::HTTP_CREATED;
            }

            $answer['course'] =
                $this->getCourseHandler()->put(
                    $course,
                    $this->getPostData($request)
                );
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
     *   section = "Course",
     *   description = "Partial Update to a Course.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CourseType",
     *   output="Ilios\CoreBundle\Entity\Course",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="\d+",
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
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $id
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
     *   section = "Course",
     *   description = "Delete a Course entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
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
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal CourseInterface $course
     *
     * @return Response
     */
    public function deleteAction($id)
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
     * @return CourseInterface $course
     */
    protected function getOr404($id)
    {
        $course = $this->getCourseHandler()
            ->findCourseBy(['id' => $id]);
        if (!$course) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $course;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('course');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return CourseHandler
     */
    protected function getCourseHandler()
    {
        return $this->container->get('ilioscore.course.handler');
    }
}
