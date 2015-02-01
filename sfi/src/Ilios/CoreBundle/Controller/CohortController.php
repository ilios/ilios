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
use Ilios\CoreBundle\Handler\CohortHandler;
use Ilios\CoreBundle\Entity\CohortInterface;

/**
 * Cohort controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("Cohort")
 */
class CohortController extends FOSRestController
{
    
    /**
     * Get a Cohort
     *
     * @ApiDoc(
     *   description = "Get a Cohort.",
     *   resource = true,
     *   requirements={
     *     {"name"="id", "dataType"="integer", "requirement"="", "description"="Cohort identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\Cohort",
     *   statusCodes={
     *     200 = "Cohort.",
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
        $answer['cohort'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all Cohort.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all Cohort.",
     *   output="Ilios\CoreBundle\Entity\Cohort",
     *   statusCodes = {
     *     200 = "List of all Cohort",
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

        $answer['cohort'] =
            $this->getCohortHandler()->findCohortsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['cohort']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a Cohort.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a Cohort.",
     *   input="Ilios\CoreBundle\Form\CohortType",
     *   output="Ilios\CoreBundle\Entity\Cohort",
     *   statusCodes={
     *     201 = "Created Cohort.",
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
            $new  =  $this->getCohortHandler()->post($request->request->all());
            $answer['cohort'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a Cohort.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a Cohort entity.",
     *   input="Ilios\CoreBundle\Form\CohortType",
     *   output="Ilios\CoreBundle\Entity\Cohort",
     *   statusCodes={
     *     200 = "Updated Cohort.",
     *     201 = "Created Cohort.",
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
            if ($cohort = $this->getCohortHandler()->findCohortBy(['id'=> $id])) {
                $answer['cohort']= $this->getCohortHandler()->put($cohort, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['cohort'] = $this->getCohortHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a Cohort.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a Cohort.",
     *   input="Ilios\CoreBundle\Form\CohortType",
     *   output="Ilios\CoreBundle\Entity\Cohort",
     *   requirements={
     *     {"name"="id", "dataType"="integer", "requirement"="", "description"="Cohort identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated Cohort.",
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
        $answer['cohort'] = $this->getCohortHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a Cohort.
     *
     * @ApiDoc(
     *   description = "Delete a Cohort entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "",
     *         "description" = "Cohort identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted Cohort.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal CohortInterface $cohort
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $cohort = $this->getOr404($id);
        try {
            $this->getCohortHandler()->deleteCohort($cohort);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return CohortInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getCohortHandler()->findCohortBy(['id' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $entity;
    }

    /**
     * @return CohortHandler
     */
    public function getCohortHandler()
    {
        return $this->container->get('ilioscore.cohort.handler');
    }
}
