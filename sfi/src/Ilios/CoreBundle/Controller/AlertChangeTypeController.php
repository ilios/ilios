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
use Ilios\CoreBundle\Handler\AlertChangeTypeHandler;
use Ilios\CoreBundle\Entity\AlertChangeTypeInterface;

/**
 * AlertChangeType controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("AlertChangeType")
 */
class AlertChangeTypeController extends FOSRestController
{
    
    /**
     * Get a AlertChangeType
     *
     * @ApiDoc(
     *   description = "Get a AlertChangeType.",
     *   resource = true,
     *   requirements={
     *     {"name"="alertChangeTypeId", "dataType"="integer", "requirement"="", "description"="AlertChangeType identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\AlertChangeType",
     *   statusCodes={
     *     200 = "AlertChangeType.",
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
        $answer['alertChangeType'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all AlertChangeType.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all AlertChangeType.",
     *   output="Ilios\CoreBundle\Entity\AlertChangeType",
     *   statusCodes = {
     *     200 = "List of all AlertChangeType",
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

        $answer['alertChangeType'] =
            $this->getAlertChangeTypeHandler()->findAlertChangeTypesBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['alertChangeType']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a AlertChangeType.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a AlertChangeType.",
     *   input="Ilios\CoreBundle\Form\AlertChangeTypeType",
     *   output="Ilios\CoreBundle\Entity\AlertChangeType",
     *   statusCodes={
     *     201 = "Created AlertChangeType.",
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
            $new  =  $this->getAlertChangeTypeHandler()->post($request->request->all());
            $answer['alertChangeType'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a AlertChangeType.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a AlertChangeType entity.",
     *   input="Ilios\CoreBundle\Form\AlertChangeTypeType",
     *   output="Ilios\CoreBundle\Entity\AlertChangeType",
     *   statusCodes={
     *     200 = "Updated AlertChangeType.",
     *     201 = "Created AlertChangeType.",
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
            if ($alertChangeType = $this->getAlertChangeTypeHandler()->findAlertChangeTypeBy(['alertChangeTypeId'=> $id])) {
                $answer['alertChangeType']= $this->getAlertChangeTypeHandler()->put($alertChangeType, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['alertChangeType'] = $this->getAlertChangeTypeHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a AlertChangeType.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a AlertChangeType.",
     *   input="Ilios\CoreBundle\Form\AlertChangeTypeType",
     *   output="Ilios\CoreBundle\Entity\AlertChangeType",
     *   requirements={
     *     {"name"="alertChangeTypeId", "dataType"="integer", "requirement"="", "description"="AlertChangeType identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated AlertChangeType.",
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
        $answer['alertChangeType'] = $this->getAlertChangeTypeHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a AlertChangeType.
     *
     * @ApiDoc(
     *   description = "Delete a AlertChangeType entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "alertChangeTypeId",
     *         "dataType" = "integer",
     *         "requirement" = "",
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
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal AlertChangeTypeInterface $alertChangeType
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $alertChangeType = $this->getOr404($id);
        try {
            $this->getAlertChangeTypeHandler()->deleteAlertChangeType($alertChangeType);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return AlertChangeTypeInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getAlertChangeTypeHandler()->findAlertChangeTypeBy(['alertChangeTypeId' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $entity;
    }

    /**
     * @return AlertChangeTypeHandler
     */
    public function getAlertChangeTypeHandler()
    {
        return $this->container->get('ilioscore.alertchangetype.handler');
    }
}
