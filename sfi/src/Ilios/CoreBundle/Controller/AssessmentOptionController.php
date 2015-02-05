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
use Ilios\CoreBundle\Handler\AssessmentOptionHandler;
use Ilios\CoreBundle\Entity\AssessmentOptionInterface;

/**
 * AssessmentOption controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("AssessmentOption")
 */
class AssessmentOptionController extends FOSRestController
{
    
    /**
     * Get a AssessmentOption
     *
     * @ApiDoc(
     *   description = "Get a AssessmentOption.",
     *   resource = true,
     *   requirements={
     *     {"name"="assessmentOptionId", "dataType"="integer", "requirement"="", "description"="AssessmentOption identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\AssessmentOption",
     *   statusCodes={
     *     200 = "AssessmentOption.",
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
        $answer['assessmentOption'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all AssessmentOption.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all AssessmentOption.",
     *   output="Ilios\CoreBundle\Entity\AssessmentOption",
     *   statusCodes = {
     *     200 = "List of all AssessmentOption",
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

        $answer['assessmentOption'] =
            $this->getAssessmentOptionHandler()->findAssessmentOptionsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['assessmentOption']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a AssessmentOption.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a AssessmentOption.",
     *   input="Ilios\CoreBundle\Form\AssessmentOptionType",
     *   output="Ilios\CoreBundle\Entity\AssessmentOption",
     *   statusCodes={
     *     201 = "Created AssessmentOption.",
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
            $new  =  $this->getAssessmentOptionHandler()->post($request->request->all());
            $answer['assessmentOption'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a AssessmentOption.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a AssessmentOption entity.",
     *   input="Ilios\CoreBundle\Form\AssessmentOptionType",
     *   output="Ilios\CoreBundle\Entity\AssessmentOption",
     *   statusCodes={
     *     200 = "Updated AssessmentOption.",
     *     201 = "Created AssessmentOption.",
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
            if ($assessmentOption = $this->getAssessmentOptionHandler()->findAssessmentOptionBy(['assessmentOptionId'=> $id])) {
                $answer['assessmentOption']= $this->getAssessmentOptionHandler()->put($assessmentOption, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['assessmentOption'] = $this->getAssessmentOptionHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a AssessmentOption.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a AssessmentOption.",
     *   input="Ilios\CoreBundle\Form\AssessmentOptionType",
     *   output="Ilios\CoreBundle\Entity\AssessmentOption",
     *   requirements={
     *     {"name"="assessmentOptionId", "dataType"="integer", "requirement"="", "description"="AssessmentOption identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated AssessmentOption.",
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
        $answer['assessmentOption'] = $this->getAssessmentOptionHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a AssessmentOption.
     *
     * @ApiDoc(
     *   description = "Delete a AssessmentOption entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "assessmentOptionId",
     *         "dataType" = "integer",
     *         "requirement" = "",
     *         "description" = "AssessmentOption identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted AssessmentOption.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal AssessmentOptionInterface $assessmentOption
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $assessmentOption = $this->getOr404($id);
        try {
            $this->getAssessmentOptionHandler()->deleteAssessmentOption($assessmentOption);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return AssessmentOptionInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getAssessmentOptionHandler()->findAssessmentOptionBy(['assessmentOptionId' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $entity;
    }

    /**
     * @return AssessmentOptionHandler
     */
    public function getAssessmentOptionHandler()
    {
        return $this->container->get('ilioscore.assessmentoption.handler');
    }
}
