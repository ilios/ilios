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
use Ilios\CoreBundle\Handler\AlertHandler;
use Ilios\CoreBundle\Entity\AlertInterface;

/**
 * Alert controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("Alert")
 */
class AlertController extends FOSRestController
{
    
    /**
     * Get a Alert
     *
     * @ApiDoc(
     *   description = "Get a Alert.",
     *   resource = true,
     *   requirements={
     *     {"name"="id", "dataType"="integer", "requirement"="", "description"="Alert identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\Alert",
     *   statusCodes={
     *     200 = "Alert.",
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
        $answer['alert'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all Alert.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all Alert.",
     *   output="Ilios\CoreBundle\Entity\Alert",
     *   statusCodes = {
     *     200 = "List of all Alert",
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

        $answer['alert'] =
            $this->getAlertHandler()->findAlertsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['alert']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a Alert.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a Alert.",
     *   input="Ilios\CoreBundle\Form\AlertType",
     *   output="Ilios\CoreBundle\Entity\Alert",
     *   statusCodes={
     *     201 = "Created Alert.",
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
            $new  =  $this->getAlertHandler()->post($request->request->all());
            $answer['alert'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a Alert.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a Alert entity.",
     *   input="Ilios\CoreBundle\Form\AlertType",
     *   output="Ilios\CoreBundle\Entity\Alert",
     *   statusCodes={
     *     200 = "Updated Alert.",
     *     201 = "Created Alert.",
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
            if ($alert = $this->getAlertHandler()->findAlertBy(['id'=> $id])) {
                $answer['alert']= $this->getAlertHandler()->put($alert, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['alert'] = $this->getAlertHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a Alert.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a Alert.",
     *   input="Ilios\CoreBundle\Form\AlertType",
     *   output="Ilios\CoreBundle\Entity\Alert",
     *   requirements={
     *     {"name"="id", "dataType"="integer", "requirement"="", "description"="Alert identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated Alert.",
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
        $answer['alert'] = $this->getAlertHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a Alert.
     *
     * @ApiDoc(
     *   description = "Delete a Alert entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "",
     *         "description" = "Alert identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted Alert.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal AlertInterface $alert
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $alert = $this->getOr404($id);
        try {
            $this->getAlertHandler()->deleteAlert($alert);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return AlertInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getAlertHandler()->findAlertBy(['id' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $entity;
    }

    /**
     * @return AlertHandler
     */
    public function getAlertHandler()
    {
        return $this->container->get('ilioscore.alert.handler');
    }
}
