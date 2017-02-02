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
use Ilios\CoreBundle\Entity\SchoolConfigInterface;

/**
 * Class SchoolConfigController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("SchoolConfigs")
 */
class SchoolConfigController extends FOSRestController
{
    /**
     * Get a School configuration item.
     *
     * @ApiDoc(
     *   section = "School Configuration",
     *   description = "Get a school configuration setting.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="School configuration setting identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\SchoolConfig",
     *   statusCodes={
     *     200 = "A school configuration setting.",
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
        $schoolConfig = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $schoolConfig)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['school_configs'][] = $schoolConfig;

        return $answer;
    }

    /**
     * Get all school configuration settings.
     *
     * @ApiDoc(
     *   section = "School Configuration",
     *   description = "Get all school configuration settings.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\SchoolConfig",
     *   statusCodes = {
     *     200 = "List of all school configuration settings.",
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

        $manager = $this->container->get('ilioscore.schoolconfig.manager');
        $result = $manager->findBy($criteria, $orderBy, $limit, $offset);

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['school_configs'] = $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a SchoolConfig.
     *
     * @ApiDoc(
     *   section = "School Configuration",
     *   description = "Create a school configuration setting.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\SchoolConfigType",
     *   output="Ilios\CoreBundle\Entity\SchoolConfig",
     *   statusCodes={
     *     201 = "Created school configuration setting.",
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
            $handler = $this->container->get('ilioscore.schoolconfig.handler');
            $schoolConfig = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $schoolConfig)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager = $this->container->get('ilioscore.schoolconfig.manager');
            $manager->update($schoolConfig, true, false);

            $answer['school_configs'] = [$schoolConfig];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a SchoolConfig.
     *
     * @ApiDoc(
     *   section = "School Configuration",
     *   description = "Update a SchoolConfig entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\SchoolConfigType",
     *   output="Ilios\CoreBundle\Entity\SchoolConfig",
     *   statusCodes={
     *     200 = "Updated SchoolConfig.",
     *     201 = "Created SchoolConfig.",
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
            $manager = $this->container->get('ilioscore.schoolconfig.manager');
            $schoolConfig = $manager->findOneBy(['id'=> $id]);
            if ($schoolConfig) {
                $code = Codes::HTTP_OK;
            } else {
                $schoolConfig = $manager->create();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->container->get('ilioscore.schoolconfig.handler');
            $schoolConfig = $handler->put($schoolConfig, $this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $schoolConfig)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager->update($schoolConfig, true, true);

            $answer['school_config'] = $schoolConfig;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a school configuration setting.
     *
     * @ApiDoc(
     *   section = "School Configuration",
     *   description = "Delete a school configuration setting.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "School configuration setting identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted school configuration setting.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal SchoolConfigInterface $schoolConfig
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $schoolConfig = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $schoolConfig)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $manager = $this->container->get('ilioscore.schoolconfig.manager');
            $manager->delete($schoolConfig);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get an entity or throw a exception.
     *
     * @param $id
     * @return SchoolConfigInterface $schoolConfig
     */
    protected function getOr404($id)
    {
        $manager = $this->container->get('ilioscore.schoolconfig.manager');
        $schoolConfig = $manager->findOneBy(['id' => $id]);
        if (!$schoolConfig) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $schoolConfig;
    }

    /**
     * Parse the request for the form data.
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('schoolConfig')) {
            return $request->request->get('schoolConfig');
        }

        return $request->request->all();
    }
}
