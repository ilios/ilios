<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Handler\ReportHandler;
use Ilios\CoreBundle\Entity\ReportInterface;

/**
 * Class ReportController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("Reports")
 */
class ReportController extends FOSRestController
{
    /**
     * Get a Report
     *
     * @ApiDoc(
     *   section = "Report",
     *   description = "Get a Report.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="Report identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\Report",
     *   statusCodes={
     *     200 = "Report.",
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
        $answer['report'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all Report.
     *
     * @ApiDoc(
     *   section = "Report",
     *   description = "Get all Report.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\Report",
     *   statusCodes = {
     *     200 = "List of all Report",
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

        $result = $this->getReportHandler()
            ->findReportsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        //If there are no matches return an empty array
        $answer['reports'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a Report.
     *
     * @ApiDoc(
     *   section = "Report",
     *   description = "Create a Report.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\ReportType",
     *   output="Ilios\CoreBundle\Entity\Report",
     *   statusCodes={
     *     201 = "Created Report.",
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
            $report = $this->getReportHandler()
                ->post($this->getPostData($request));

            $response = new Response();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set(
                'Location',
                $this->generateUrl(
                    'get_reports',
                    ['id' => $report->getId()],
                    true
                )
            );

            return $response;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a Report.
     *
     * @ApiDoc(
     *   section = "Report",
     *   description = "Update a Report entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\ReportType",
     *   output="Ilios\CoreBundle\Entity\Report",
     *   statusCodes={
     *     200 = "Updated Report.",
     *     201 = "Created Report.",
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
            $report = $this->getReportHandler()
                ->findReportBy(['id'=> $id]);
            if ($report) {
                $code = Codes::HTTP_OK;
            } else {
                $report = $this->getReportHandler()->createReport();
                $code = Codes::HTTP_CREATED;
            }

            $answer['report'] =
                $this->getReportHandler()->put(
                    $report,
                    $this->getPostData($request)
                );
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
     *   section = "Report",
     *   description = "Partial Update to a Report.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\ReportType",
     *   output="Ilios\CoreBundle\Entity\Report",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="\d+",
     *         "description"="Report identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated Report.",
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
    public function patchAction(Request $request, $id)
    {
        $answer['report'] =
            $this->getReportHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a Report.
     *
     * @ApiDoc(
     *   section = "Report",
     *   description = "Delete a Report entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
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
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal ReportInterface $report
     *
     * @return Response
     */
    public function deleteAction($id)
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
     * @return ReportInterface $report
     */
    protected function getOr404($id)
    {
        $report = $this->getReportHandler()
            ->findReportBy(['id' => $id]);
        if (!$report) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $report;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('report');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return ReportHandler
     */
    protected function getReportHandler()
    {
        return $this->container->get('ilioscore.report.handler');
    }
}
