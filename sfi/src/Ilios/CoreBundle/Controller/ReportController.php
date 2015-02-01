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
use Ilios\CoreBundle\Handler\ReportHandler;
use Ilios\CoreBundle\Entity\ReportInterface;

/**
 * Report controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("Report")
 */
class ReportController extends FOSRestController
{
    
    /**
     * Get a Report
     *
     * @ApiDoc(
     *   description = "Get a Report.",
     *   resource = true,
     *   requirements={
     *     {"name"="reportId", "dataType"="integer", "requirement"="", "description"="Report identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\Report",
     *   statusCodes={
     *     200 = "Report.",
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
        $answer['report'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all Report.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all Report.",
     *   output="Ilios\CoreBundle\Entity\Report",
     *   statusCodes = {
     *     200 = "List of all Report",
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

        $answer['report'] =
            $this->getReportHandler()->findReportsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['report']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a Report.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a Report.",
     *   input="Ilios\CoreBundle\Form\ReportType",
     *   output="Ilios\CoreBundle\Entity\Report",
     *   statusCodes={
     *     201 = "Created Report.",
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
            $new  =  $this->getReportHandler()->post($request->request->all());
            $answer['report'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a Report.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a Report entity.",
     *   input="Ilios\CoreBundle\Form\ReportType",
     *   output="Ilios\CoreBundle\Entity\Report",
     *   statusCodes={
     *     200 = "Updated Report.",
     *     201 = "Created Report.",
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
            if ($report = $this->getReportHandler()->findReportBy(['reportId'=> $id])) {
                $answer['report']= $this->getReportHandler()->put($report, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['report'] = $this->getReportHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a Report.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a Report.",
     *   input="Ilios\CoreBundle\Form\ReportType",
     *   output="Ilios\CoreBundle\Entity\Report",
     *   requirements={
     *     {"name"="reportId", "dataType"="integer", "requirement"="", "description"="Report identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated Report.",
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
        $answer['report'] = $this->getReportHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a Report.
     *
     * @ApiDoc(
     *   description = "Delete a Report entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "reportId",
     *         "dataType" = "integer",
     *         "requirement" = "",
     *         "description" = "Report identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted Report.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal ReportInterface $report
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $report = $this->getOr404($id);
        try {
            $this->getReportHandler()->deleteReport($report);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return ReportInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getReportHandler()->findReportBy(['reportId' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $entity;
    }

    /**
     * @return ReportHandler
     */
    public function getReportHandler()
    {
        return $this->container->get('ilioscore.report.handler');
    }
}
