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
use Ilios\CoreBundle\Entity\ApplicationConfigInterface;

/**
 * Class ApplicationConfigController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("ApplicationConfigs")
 */
class ApplicationConfigController extends FOSRestController
{
    /**
     * Get a Application configuration item.
     *
     * @ApiDoc(
     *   section = "Application Configuration",
     *   description = "Get a application configuration setting.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="Application configuration setting identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\ApplicationConfig",
     *   statusCodes={
     *     200 = "A application configuration setting.",
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
        $applicationConfig = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $applicationConfig)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['applicationConfigs'][] = $applicationConfig;

        return $answer;
    }

    /**
     * Get all application configuration settings.
     *
     * @ApiDoc(
     *   section = "Application Configuration",
     *   description = "Get all application configuration settings.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\ApplicationConfig",
     *   statusCodes = {
     *     200 = "List of all application configuration settings.",
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

        $manager = $this->container->get('ilioscore.applicationconfig.manager');
        $result = $manager->findBy($criteria, $orderBy, $limit, $offset);

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['applicationConfigs'] = $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a ApplicationConfig.
     *
     * @ApiDoc(
     *   section = "Application Configuration",
     *   description = "Create a application configuration setting.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\ApplicationConfigType",
     *   output="Ilios\CoreBundle\Entity\ApplicationConfig",
     *   statusCodes={
     *     201 = "Created application configuration setting.",
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
            $handler = $this->container->get('ilioscore.applicationconfig.handler');
            $applicationConfig = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $applicationConfig)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager = $this->container->get('ilioscore.applicationconfig.manager');
            $manager->update($applicationConfig, true, false);

            $answer['applicationConfigs'] = [$applicationConfig];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a ApplicationConfig.
     *
     * @ApiDoc(
     *   section = "Application Configuration",
     *   description = "Update an application config setting.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\ApplicationConfigType",
     *   output="Ilios\CoreBundle\Entity\ApplicationConfig",
     *   statusCodes={
     *     200 = "Updated ApplicationConfig.",
     *     201 = "Created ApplicationConfig.",
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
            $manager = $this->container->get('ilioscore.applicationconfig.manager');
            $applicationConfig = $manager->findOneBy(['id'=> $id]);
            if ($applicationConfig) {
                $code = Codes::HTTP_OK;
            } else {
                $applicationConfig = $manager->create();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->container->get('ilioscore.applicationconfig.handler');
            $applicationConfig = $handler->put($applicationConfig, $this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $applicationConfig)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager->update($applicationConfig, true, true);

            $answer['applicationConfig'] = $applicationConfig;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a application configuration setting.
     *
     * @ApiDoc(
     *   section = "Application Configuration",
     *   description = "Delete a application configuration setting.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "Application configuration setting identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted application configuration setting.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal ApplicationConfigInterface $applicationConfig
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $applicationConfig = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $applicationConfig)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $manager = $this->container->get('ilioscore.applicationconfig.manager');
            $manager->delete($applicationConfig);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get an entity or throw a exception.
     *
     * @param $id
     * @return ApplicationConfigInterface $applicationConfig
     */
    protected function getOr404($id)
    {
        $manager = $this->container->get('ilioscore.applicationconfig.manager');
        $applicationConfig = $manager->findOneBy(['id' => $id]);
        if (!$applicationConfig) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $applicationConfig;
    }

    /**
     * Parse the request for the form data.
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('applicationConfig')) {
            return $request->request->get('applicationConfig');
        }

        return $request->request->all();
    }
}
