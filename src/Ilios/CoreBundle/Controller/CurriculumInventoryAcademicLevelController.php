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
use Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevelInterface;

/**
 * Class CurriculumInventoryAcademicLevelController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("CurriculumInventoryAcademicLevels")
 */
class CurriculumInventoryAcademicLevelController extends FOSRestController
{
    /**
     * Get a CurriculumInventoryAcademicLevel
     *
     * @ApiDoc(
     *   section = "CurriculumInventoryAcademicLevel",
     *   description = "Get a CurriculumInventoryAcademicLevel.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="CurriculumInventoryAcademicLevel identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel",
     *   statusCodes={
     *     200 = "CurriculumInventoryAcademicLevel.",
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
        $curriculumInventoryAcademicLevel = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $curriculumInventoryAcademicLevel)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['curriculumInventoryAcademicLevels'][] = $curriculumInventoryAcademicLevel;

        return $answer;
    }

    /**
     * Get all CurriculumInventoryAcademicLevel.
     *
     * @ApiDoc(
     *   section = "CurriculumInventoryAcademicLevel",
     *   description = "Get all CurriculumInventoryAcademicLevel.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel",
     *   statusCodes = {
     *     200 = "List of all CurriculumInventoryAcademicLevel",
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

        $manager = $this->container->get('ilioscore.curriculuminventoryacademiclevel.manager');
        $result = $manager->findBy($criteria, $orderBy, $limit, $offset);

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['curriculumInventoryAcademicLevels'] =
            $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a CurriculumInventoryAcademicLevel.
     *
     * @ApiDoc(
     *   section = "CurriculumInventoryAcademicLevel",
     *   description = "Create a CurriculumInventoryAcademicLevel.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CurriculumInventoryAcademicLevelType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel",
     *   statusCodes={
     *     201 = "Created CurriculumInventoryAcademicLevel.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   },
     *   deprecated = true
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
            $handler = $this->container->get('ilioscore.curriculuminventoryacademiclevel.handler');

            $curriculumInventoryAcademicLevel = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $curriculumInventoryAcademicLevel)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager = $this->container->get('ilioscore.curriculuminventoryacademiclevel.manager');
            $manager->update($curriculumInventoryAcademicLevel, true, false);

            $answer['curriculumInventoryAcademicLevels'] = [$curriculumInventoryAcademicLevel];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a CurriculumInventoryAcademicLevel.
     *
     * @ApiDoc(
     *   section = "CurriculumInventoryAcademicLevel",
     *   description = "Update a CurriculumInventoryAcademicLevel entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CurriculumInventoryAcademicLevelType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel",
     *   statusCodes={
     *     200 = "Updated CurriculumInventoryAcademicLevel.",
     *     201 = "Created CurriculumInventoryAcademicLevel.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   },
     *   deprecated = true
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
            $manager = $this->container->get('ilioscore.curriculuminventoryacademiclevel.manager');
            $curriculumInventoryAcademicLevel = $manager->findOneBy(['id'=> $id]);
            if ($curriculumInventoryAcademicLevel) {
                $code = Codes::HTTP_OK;
            } else {
                $curriculumInventoryAcademicLevel = $manager->create();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->container->get('ilioscore.curriculuminventoryacademiclevel.handler');
            $curriculumInventoryAcademicLevel = $handler->put(
                $curriculumInventoryAcademicLevel,
                $this->getPostData($request)
            );

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $curriculumInventoryAcademicLevel)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager->update($curriculumInventoryAcademicLevel, true, true);

            $answer['curriculumInventoryAcademicLevel'] = $curriculumInventoryAcademicLevel;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a CurriculumInventoryAcademicLevel.
     *
     * @ApiDoc(
     *   section = "CurriculumInventoryAcademicLevel",
     *   description = "Delete a CurriculumInventoryAcademicLevel entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "CurriculumInventoryAcademicLevel identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted CurriculumInventoryAcademicLevel.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   },
     *   deprecated = true
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $curriculumInventoryAcademicLevel = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $curriculumInventoryAcademicLevel)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $manager = $this->container->get('ilioscore.curriculuminventoryacademiclevel.manager');
            $manager->delete($curriculumInventoryAcademicLevel);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel
     */
    protected function getOr404($id)
    {
        $manager = $this->container->get('ilioscore.curriculuminventoryacademiclevel.manager');
        $curriculumInventoryAcademicLevel = $manager->findOneBy(['id' => $id]);
        if (!$curriculumInventoryAcademicLevel) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $curriculumInventoryAcademicLevel;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('curriculumInventoryAcademicLevel')) {
            return $request->request->get('curriculumInventoryAcademicLevel');
        }

        return $request->request->all();
    }
}
