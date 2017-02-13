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
use Ilios\CoreBundle\Entity\AlertChangeTypeInterface;

/**
 * Class AlertChangeTypeController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("AlertChangeTypes")
 */
class AlertChangeTypeController
{
    /**
     * Get a AlertChangeType
     *
     * @ApiDoc(
     *   section = "AlertChangeType",
     *   description = "Get a AlertChangeType.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="AlertChangeType identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\AlertChangeType",
     *   statusCodes={
     *     200 = "AlertChangeType.",
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

        $manager = $this->container->get('ilioscore.alertchangetype.manager');

        $alertChangeType = $manager->findDTOBy(['id' => $id]);
        if (!$alertChangeType) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $alertChangeType)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['alertChangeTypes'][] = $alertChangeType;

        return $answer;
    }

    /**
     * Get all AlertChangeType.
     *
     * @ApiDoc(
     *   section = "AlertChangeType",
     *   description = "Get all AlertChangeType.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\AlertChangeType",
     *   statusCodes = {
     *     200 = "List of all AlertChangeType",
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

        $manager = $this->container->get('ilioscore.alertchangetype.manager');
        $result = $manager->findDTOsBy($criteria, $orderBy, $limit, $offset);

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['alertChangeTypes'] =
            $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a AlertChangeType.
     *
     * @ApiDoc(
     *   section = "AlertChangeType",
     *   description = "Create a AlertChangeType.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\AlertChangeTypeType",
     *   output="Ilios\CoreBundle\Entity\AlertChangeType",
     *   statusCodes={
     *     201 = "Created AlertChangeType.",
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
            $handler = $this->container->get('ilioscore.alertchangetype.handler');

            $alertChangeType = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $alertChangeType)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager = $this->container->get('ilioscore.alertchangetype.manager');
            $manager->update($alertChangeType, true, false);

            $answer['alertChangeTypes'] = [$alertChangeType];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a AlertChangeType.
     *
     * @ApiDoc(
     *   section = "AlertChangeType",
     *   description = "Update a AlertChangeType entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\AlertChangeTypeType",
     *   output="Ilios\CoreBundle\Entity\AlertChangeType",
     *   statusCodes={
     *     200 = "Updated AlertChangeType.",
     *     201 = "Created AlertChangeType.",
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
            $manager = $this->container->get('ilioscore.alertchangetype.manager');
            $alertChangeType = $manager->findOneBy(['id'=> $id]);
            if ($alertChangeType) {
                $code = Codes::HTTP_OK;
            } else {
                $alertChangeType = $manager->create();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->container->get('ilioscore.alertchangetype.handler');
            $alertChangeType = $handler->put($alertChangeType, $this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $alertChangeType)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager->update($alertChangeType, true, true);

            $answer['alertChangeType'] = $alertChangeType;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a AlertChangeType.
     *
     * @ApiDoc(
     *   section = "AlertChangeType",
     *   description = "Delete a AlertChangeType entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "AlertChangeType identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted AlertChangeType.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal AlertChangeTypeInterface $alertChangeType
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $alertChangeType = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $alertChangeType)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $manager = $this->container->get('ilioscore.alertchangetype.manager');
            $manager->delete($alertChangeType);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return AlertChangeTypeInterface $alertChangeType
     */
    protected function getOr404($id)
    {
        $manager = $this->container->get('ilioscore.alertchangetype.manager');
        $alertChangeType = $manager->findOneBy(['id' => $id]);
        if (!$alertChangeType) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $alertChangeType;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('alertChangeType')) {
            return $request->request->get('alertChangeType');
        }

        return $request->request->all();
    }
}
