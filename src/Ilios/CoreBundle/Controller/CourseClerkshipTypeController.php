<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Entity\CourseClerkshipTypeInterface;

/**
 * Class CourseClerkshipTypeController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("CourseClerkshipTypes")
 */
class CourseClerkshipTypeController extends FOSRestController
{
    /**
     * Get a CourseClerkshipType
     *
     * @ApiDoc(
     *   section = "CourseClerkshipType",
     *   description = "Get a CourseClerkshipType.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="CourseClerkshipType identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\CourseClerkshipType",
     *   statusCodes={
     *     200 = "CourseClerkshipType.",
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
        $courseClerkshipType = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $courseClerkshipType)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['courseClerkshipTypes'][] = $courseClerkshipType;

        return $answer;
    }

    /**
     * Get all CourseClerkshipType.
     *
     * @ApiDoc(
     *   section = "CourseClerkshipType",
     *   description = "Get all CourseClerkshipType.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\CourseClerkshipType",
     *   statusCodes = {
     *     200 = "List of all CourseClerkshipType",
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

        $manager = $this->container->get('ilioscore.courseclerkshiptype.manager');
        $result = $manager->findBy($criteria, $orderBy, $limit, $offset);

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['courseClerkshipTypes'] =
            $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a CourseClerkshipType.
     *
     * @ApiDoc(
     *   section = "CourseClerkshipType",
     *   description = "Create a CourseClerkshipType.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CourseClerkshipTypeType",
     *   output="Ilios\CoreBundle\Entity\CourseClerkshipType",
     *   statusCodes={
     *     201 = "Created CourseClerkshipType.",
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
            $handler = $this->container->get('ilioscore.courseclerkshiptype.handler');
            $courseClerkshipType = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $courseClerkshipType)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager = $this->container->get('ilioscore.courseclerkshiptype.manager');
            $manager->update($courseClerkshipType, true, false);

            $answer['courseClerkshipTypes'] = [$courseClerkshipType];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a CourseClerkshipType.
     *
     * @ApiDoc(
     *   section = "CourseClerkshipType",
     *   description = "Update a CourseClerkshipType entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CourseClerkshipTypeType",
     *   output="Ilios\CoreBundle\Entity\CourseClerkshipType",
     *   statusCodes={
     *     200 = "Updated CourseClerkshipType.",
     *     201 = "Created CourseClerkshipType.",
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
            $manager = $this->container->get('ilioscore.courseclerkshiptype.manager');
            $courseClerkshipType = $manager->findOneBy(['id'=> $id]);
            if ($courseClerkshipType) {
                $code = Codes::HTTP_OK;
            } else {
                $courseClerkshipType = $manager->create();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->container->get('ilioscore.courseclerkshiptype.handler');
            $courseClerkshipType = $handler->put($courseClerkshipType, $this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $courseClerkshipType)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager->update($courseClerkshipType, true, true);

            $answer['courseClerkshipType'] = $courseClerkshipType;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a CourseClerkshipType.
     *
     * @ApiDoc(
     *   section = "CourseClerkshipType",
     *   description = "Delete a CourseClerkshipType entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
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
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal CourseClerkshipTypeInterface $courseClerkshipType
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $courseClerkshipType = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $courseClerkshipType)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $manager = $this->container->get('ilioscore.courseclerkshiptype.manager');
            $manager->delete($courseClerkshipType);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return CourseClerkshipTypeInterface $courseClerkshipType
     */
    protected function getOr404($id)
    {
        $manager = $this->container->get('ilioscore.courseclerkshiptype.manager');
        $courseClerkshipType = $manager->findOneBy(['id' => $id]);
        if (!$courseClerkshipType) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $courseClerkshipType;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('courseClerkshipType')) {
            return $request->request->get('courseClerkshipType');
        }

        return $request->request->all();
    }
}
