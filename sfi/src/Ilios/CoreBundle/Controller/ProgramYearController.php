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
use Ilios\CoreBundle\Handler\ProgramYearHandler;
use Ilios\CoreBundle\Entity\ProgramYearInterface;

/**
 * ProgramYear controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("ProgramYear")
 */
class ProgramYearController extends FOSRestController
{
    
    /**
     * Get a ProgramYear
     *
     * @ApiDoc(
     *   description = "Get a ProgramYear.",
     *   resource = true,
     *   requirements={
     *     {"name"="id", "dataType"="integer", "requirement"="", "description"="ProgramYear identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\ProgramYear",
     *   statusCodes={
     *     200 = "ProgramYear.",
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
        $answer['programYear'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all ProgramYear.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all ProgramYear.",
     *   output="Ilios\CoreBundle\Entity\ProgramYear",
     *   statusCodes = {
     *     200 = "List of all ProgramYear",
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

        $answer['programYear'] =
            $this->getProgramYearHandler()->findProgramYearsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['programYear']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a ProgramYear.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a ProgramYear.",
     *   input="Ilios\CoreBundle\Form\ProgramYearType",
     *   output="Ilios\CoreBundle\Entity\ProgramYear",
     *   statusCodes={
     *     201 = "Created ProgramYear.",
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
            $new  =  $this->getProgramYearHandler()->post($request->request->all());
            $answer['programYear'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a ProgramYear.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a ProgramYear entity.",
     *   input="Ilios\CoreBundle\Form\ProgramYearType",
     *   output="Ilios\CoreBundle\Entity\ProgramYear",
     *   statusCodes={
     *     200 = "Updated ProgramYear.",
     *     201 = "Created ProgramYear.",
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
            if ($programYear = $this->getProgramYearHandler()->findProgramYearBy(['id'=> $id])) {
                $answer['programYear']= $this->getProgramYearHandler()->put($programYear, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['programYear'] = $this->getProgramYearHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a ProgramYear.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a ProgramYear.",
     *   input="Ilios\CoreBundle\Form\ProgramYearType",
     *   output="Ilios\CoreBundle\Entity\ProgramYear",
     *   requirements={
     *     {"name"="id", "dataType"="integer", "requirement"="", "description"="ProgramYear identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated ProgramYear.",
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
        $answer['programYear'] = $this->getProgramYearHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a ProgramYear.
     *
     * @ApiDoc(
     *   description = "Delete a ProgramYear entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "",
     *         "description" = "ProgramYear identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted ProgramYear.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal ProgramYearInterface $programYear
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $programYear = $this->getOr404($id);
        try {
            $this->getProgramYearHandler()->deleteProgramYear($programYear);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return ProgramYearInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getProgramYearHandler()->findProgramYearBy(['id' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $entity;
    }

    /**
     * @return ProgramYearHandler
     */
    public function getProgramYearHandler()
    {
        return $this->container->get('ilioscore.programyear.handler');
    }
}
