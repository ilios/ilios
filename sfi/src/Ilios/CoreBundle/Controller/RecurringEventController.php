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
use Ilios\CoreBundle\Handler\RecurringEventHandler;
use Ilios\CoreBundle\Entity\RecurringEventInterface;

/**
 * RecurringEvent controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("RecurringEvent")
 */
class RecurringEventController extends FOSRestController
{
    
    /**
     * Get a RecurringEvent
     *
     * @ApiDoc(
     *   description = "Get a RecurringEvent.",
     *   resource = true,
     *   requirements={
     *     {"name"="recurringEventId", "dataType"="integer", "requirement"="", "description"="RecurringEvent identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\RecurringEvent",
     *   statusCodes={
     *     200 = "RecurringEvent.",
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
        $answer['recurringEvent'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all RecurringEvent.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all RecurringEvent.",
     *   output="Ilios\CoreBundle\Entity\RecurringEvent",
     *   statusCodes = {
     *     200 = "List of all RecurringEvent",
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

        $answer['recurringEvent'] =
            $this->getRecurringEventHandler()->findRecurringEventsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['recurringEvent']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a RecurringEvent.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a RecurringEvent.",
     *   input="Ilios\CoreBundle\Form\RecurringEventType",
     *   output="Ilios\CoreBundle\Entity\RecurringEvent",
     *   statusCodes={
     *     201 = "Created RecurringEvent.",
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
            $new  =  $this->getRecurringEventHandler()->post($request->request->all());
            $answer['recurringEvent'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a RecurringEvent.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a RecurringEvent entity.",
     *   input="Ilios\CoreBundle\Form\RecurringEventType",
     *   output="Ilios\CoreBundle\Entity\RecurringEvent",
     *   statusCodes={
     *     200 = "Updated RecurringEvent.",
     *     201 = "Created RecurringEvent.",
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
            if ($recurringEvent = $this->getRecurringEventHandler()->findRecurringEventBy(['recurringEventId'=> $id])) {
                $answer['recurringEvent']= $this->getRecurringEventHandler()->put($recurringEvent, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['recurringEvent'] = $this->getRecurringEventHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a RecurringEvent.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a RecurringEvent.",
     *   input="Ilios\CoreBundle\Form\RecurringEventType",
     *   output="Ilios\CoreBundle\Entity\RecurringEvent",
     *   requirements={
     *     {"name"="recurringEventId", "dataType"="integer", "requirement"="", "description"="RecurringEvent identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated RecurringEvent.",
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
        $answer['recurringEvent'] = $this->getRecurringEventHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a RecurringEvent.
     *
     * @ApiDoc(
     *   description = "Delete a RecurringEvent entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "recurringEventId",
     *         "dataType" = "integer",
     *         "requirement" = "",
     *         "description" = "RecurringEvent identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted RecurringEvent.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal RecurringEventInterface $recurringEvent
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $recurringEvent = $this->getOr404($id);
        try {
            $this->getRecurringEventHandler()->deleteRecurringEvent($recurringEvent);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return RecurringEventInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getRecurringEventHandler()->findRecurringEventBy(['recurringEventId' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $entity;
    }

    /**
     * @return RecurringEventHandler
     */
    public function getRecurringEventHandler()
    {
        return $this->container->get('ilioscore.recurringevent.handler');
    }
}
