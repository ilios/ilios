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
use Ilios\CoreBundle\Handler\ObjectiveHandler;
use Ilios\CoreBundle\Entity\ObjectiveInterface;

/**
 * Objective controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("Objective")
 */
class ObjectiveController extends FOSRestController
{
    
    /**
     * Get a Objective
     *
     * @ApiDoc(
     *   description = "Get a Objective.",
     *   resource = true,
     *   requirements={
     *     {"name"="id", "dataType"="integer", "requirement"="", "description"="Objective identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\Objective",
     *   statusCodes={
     *     200 = "Objective.",
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
        $answer['objective'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all Objective.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all Objective.",
     *   output="Ilios\CoreBundle\Entity\Objective",
     *   statusCodes = {
     *     200 = "List of all Objective",
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

        $answer['objective'] =
            $this->getObjectiveHandler()->findObjectivesBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['objective']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a Objective.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a Objective.",
     *   input="Ilios\CoreBundle\Form\ObjectiveType",
     *   output="Ilios\CoreBundle\Entity\Objective",
     *   statusCodes={
     *     201 = "Created Objective.",
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
            $new  =  $this->getObjectiveHandler()->post($request->request->all());
            $answer['objective'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a Objective.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a Objective entity.",
     *   input="Ilios\CoreBundle\Form\ObjectiveType",
     *   output="Ilios\CoreBundle\Entity\Objective",
     *   statusCodes={
     *     200 = "Updated Objective.",
     *     201 = "Created Objective.",
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
            if ($objective = $this->getObjectiveHandler()->findObjectiveBy(['id'=> $id])) {
                $answer['objective']= $this->getObjectiveHandler()->put($objective, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['objective'] = $this->getObjectiveHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a Objective.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a Objective.",
     *   input="Ilios\CoreBundle\Form\ObjectiveType",
     *   output="Ilios\CoreBundle\Entity\Objective",
     *   requirements={
     *     {"name"="id", "dataType"="integer", "requirement"="", "description"="Objective identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated Objective.",
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
        $answer['objective'] = $this->getObjectiveHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a Objective.
     *
     * @ApiDoc(
     *   description = "Delete a Objective entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "",
     *         "description" = "Objective identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted Objective.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal ObjectiveInterface $objective
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $objective = $this->getOr404($id);
        try {
            $this->getObjectiveHandler()->deleteObjective($objective);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return ObjectiveInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getObjectiveHandler()->findObjectiveBy(['id' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $entity;
    }

    /**
     * @return ObjectiveHandler
     */
    public function getObjectiveHandler()
    {
        return $this->container->get('ilioscore.objective.handler');
    }
}
