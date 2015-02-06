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
use Ilios\CoreBundle\Handler\CompetencyHandler;
use Ilios\CoreBundle\Entity\CompetencyInterface;

/**
 * Competency controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("Competency")
 */
class CompetencyController extends FOSRestController
{

    /**
     * Get a Competency
     *
     * @ApiDoc(
     *   description = "Get a Competency.",
     *   resource = true,
     *   requirements={
     *     {"name"="id", "dataType"="integer", "requirement"="", "description"="Competency identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\Competency",
     *   statusCodes={
     *     200 = "Competency.",
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
        $answer['competency'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all Competency.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all Competency.",
     *   output="Ilios\CoreBundle\Entity\Competency",
     *   statusCodes = {
     *     200 = "List of all Competency",
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

        $answer['competencies'] =
            $this->getCompetencyHandler()->findCompetenciesBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['competencies']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a Competency.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a Competency.",
     *   input="Ilios\CoreBundle\Form\CompetencyType",
     *   output="Ilios\CoreBundle\Entity\Competency",
     *   statusCodes={
     *     201 = "Created Competency.",
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
            $new  =  $this->getCompetencyHandler()->post($request->request->all());
            $answer['competency'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a Competency.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a Competency entity.",
     *   input="Ilios\CoreBundle\Form\CompetencyType",
     *   output="Ilios\CoreBundle\Entity\Competency",
     *   statusCodes={
     *     200 = "Updated Competency.",
     *     201 = "Created Competency.",
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
            if ($competency = $this->getCompetencyHandler()->findCompetencyBy(['id'=> $id])) {
                $answer['competency']= $this->getCompetencyHandler()->put($competency, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['competency'] = $this->getCompetencyHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a Competency.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a Competency.",
     *   input="Ilios\CoreBundle\Form\CompetencyType",
     *   output="Ilios\CoreBundle\Entity\Competency",
     *   requirements={
     *     {"name"="id", "dataType"="integer", "requirement"="", "description"="Competency identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated Competency.",
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
        $answer['competency'] = $this->getCompetencyHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a Competency.
     *
     * @ApiDoc(
     *   description = "Delete a Competency entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "",
     *         "description" = "Competency identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted Competency.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal CompetencyInterface $competency
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $competency = $this->getOr404($id);
        try {
            $this->getCompetencyHandler()->deleteCompetency($competency);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return CompetencyInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getCompetencyHandler()->findCompetencyBy(['id' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $entity;
    }

    /**
     * @return CompetencyHandler
     */
    public function getCompetencyHandler()
    {
        return $this->container->get('ilioscore.competency.handler');
    }
}
