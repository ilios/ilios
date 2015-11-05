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
use Ilios\CoreBundle\Handler\DepartmentHandler;
use Ilios\CoreBundle\Entity\DepartmentInterface;

/**
 * Class DepartmentController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("Departments")
 */
class DepartmentController extends FOSRestController
{
    /**
     * Get a Department
     *
     * @ApiDoc(
     *   section = "Department",
     *   description = "Get a Department.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="Department identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\Department",
     *   statusCodes={
     *     200 = "Department.",
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
        $department = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $department)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['departments'][] = $department;

        return $answer;
    }

    /**
     * Get all Department.
     *
     * @ApiDoc(
     *   section = "Department",
     *   description = "Get all Department.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\Department",
     *   statusCodes = {
     *     200 = "List of all Department",
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

        // @todo delete once https://github.com/ilios/moodle-enrol-ilios/pull/10 lands. [ST 2015/11/04]
        if (array_key_exists('deleted', $criteria)) {
            unset($criteria['deleted']);
        }

        $result = $this->getDepartmentHandler()
            ->findDepartmentsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['departments'] =
            $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a Department.
     *
     * @ApiDoc(
     *   section = "Department",
     *   description = "Create a Department.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\DepartmentType",
     *   output="Ilios\CoreBundle\Entity\Department",
     *   statusCodes={
     *     201 = "Created Department.",
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
            $handler = $this->getDepartmentHandler();

            $department = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $department)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getDepartmentHandler()->updateDepartment($department, true, false);

            $answer['departments'] = [$department];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a Department.
     *
     * @ApiDoc(
     *   section = "Department",
     *   description = "Update a Department entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\DepartmentType",
     *   output="Ilios\CoreBundle\Entity\Department",
     *   statusCodes={
     *     200 = "Updated Department.",
     *     201 = "Created Department.",
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
            $department = $this->getDepartmentHandler()
                ->findDepartmentBy(['id'=> $id]);
            if ($department) {
                $code = Codes::HTTP_OK;
            } else {
                $department = $this->getDepartmentHandler()
                    ->createDepartment();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->getDepartmentHandler();

            $department = $handler->put(
                $department,
                $this->getPostData($request)
            );

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $department)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getDepartmentHandler()->updateDepartment($department, true, true);

            $answer['department'] = $department;

        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a Department.
     *
     * @ApiDoc(
     *   section = "Department",
     *   description = "Delete a Department entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
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
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal DepartmentInterface $department
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $department = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $department)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $this->getDepartmentHandler()
                ->deleteDepartment($department);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return DepartmentInterface $department
     */
    protected function getOr404($id)
    {
        $department = $this->getDepartmentHandler()
            ->findDepartmentBy(['id' => $id]);
        if (!$department) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $department;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('department')) {
            return $request->request->get('department');
        }

        return $request->request->all();
    }

    /**
     * @return DepartmentHandler
     */
    protected function getDepartmentHandler()
    {
        return $this->container->get('ilioscore.department.handler');
    }
}
