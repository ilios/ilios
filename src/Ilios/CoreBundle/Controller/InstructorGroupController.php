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
use Ilios\CoreBundle\Entity\InstructorGroupInterface;

/**
 * Class InstructorGroupController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("InstructorGroups")
 */
class InstructorGroupController
{
    /**
     * Get a InstructorGroup
     *
     * @ApiDoc(
     *   section = "InstructorGroup",
     *   description = "Get a InstructorGroup.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="InstructorGroup identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\InstructorGroup",
     *   statusCodes={
     *     200 = "InstructorGroup.",
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
        $instructorGroup = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $instructorGroup)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['instructorGroups'][] = $instructorGroup;

        return $answer;
    }

    /**
     * Get all InstructorGroup.
     *
     * @ApiDoc(
     *   section = "InstructorGroup",
     *   description = "Get all InstructorGroup.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\InstructorGroup",
     *   statusCodes = {
     *     200 = "List of all InstructorGroup",
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

        $manager = $this->container->get('ilioscore.instructorgroup.manager');
        $result = $manager->findBy($criteria, $orderBy, $limit, $offset);

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['instructorGroups'] =
            $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a InstructorGroup.
     *
     * @ApiDoc(
     *   section = "InstructorGroup",
     *   description = "Create a InstructorGroup.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\InstructorGroupType",
     *   output="Ilios\CoreBundle\Entity\InstructorGroup",
     *   statusCodes={
     *     201 = "Created InstructorGroup.",
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
            $handler = $this->container->get('ilioscore.instructorgroup.handler');
            $instructorGroup = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $instructorGroup)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager = $this->container->get('ilioscore.instructorgroup.manager');
            $manager->update($instructorGroup, true, false);

            $answer['instructorGroups'] = [$instructorGroup];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a InstructorGroup.
     *
     * @ApiDoc(
     *   section = "InstructorGroup",
     *   description = "Update a InstructorGroup entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\InstructorGroupType",
     *   output="Ilios\CoreBundle\Entity\InstructorGroup",
     *   statusCodes={
     *     200 = "Updated InstructorGroup.",
     *     201 = "Created InstructorGroup.",
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
            $manager = $this->container->get('ilioscore.instructorgroup.manager');
            $instructorGroup = $manager->findOneBy(['id'=> $id]);
            if ($instructorGroup) {
                $code = Codes::HTTP_OK;
            } else {
                $instructorGroup = $manager->create();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->container->get('ilioscore.instructorgroup.handler');

            $instructorGroup = $handler->put($instructorGroup, $this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $instructorGroup)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager->update($instructorGroup, true, true);

            $answer['instructorGroup'] = $instructorGroup;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a InstructorGroup.
     *
     * @ApiDoc(
     *   section = "InstructorGroup",
     *   description = "Delete a InstructorGroup entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "InstructorGroup identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted InstructorGroup.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal InstructorGroupInterface $instructorGroup
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $instructorGroup = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $instructorGroup)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $manager = $this->container->get('ilioscore.instructorgroup.manager');
            $manager->delete($instructorGroup);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return InstructorGroupInterface $instructorGroup
     */
    protected function getOr404($id)
    {
        $manager = $this->container->get('ilioscore.instructorgroup.manager');
        $instructorGroup = $manager->findOneBy(['id' => $id]);
        if (!$instructorGroup) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $instructorGroup;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('instructorGroup')) {
            return $request->request->get('instructorGroup');
        }

        return $request->request->all();
    }
}
