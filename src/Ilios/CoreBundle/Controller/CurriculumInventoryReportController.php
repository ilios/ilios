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
use Ilios\CoreBundle\Handler\CurriculumInventoryReportHandler;
use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;

/**
 * Class CurriculumInventoryReportController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("CurriculumInventoryReports")
 */
class CurriculumInventoryReportController extends FOSRestController
{
    /**
     * Get a CurriculumInventoryReport
     *
     * @ApiDoc(
     *   section = "CurriculumInventoryReport",
     *   description = "Get a CurriculumInventoryReport.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="CurriculumInventoryReport identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryReport",
     *   statusCodes={
     *     200 = "CurriculumInventoryReport.",
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
        $curriculumInventoryReport = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $curriculumInventoryReport)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['curriculumInventoryReports'][] = $curriculumInventoryReport;

        return $answer;
    }

    /**
     * Get all CurriculumInventoryReport.
     *
     * @ApiDoc(
     *   section = "CurriculumInventoryReport",
     *   description = "Get all CurriculumInventoryReport.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryReport",
     *   statusCodes = {
     *     200 = "List of all CurriculumInventoryReport",
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

        $result = $this->getCurriculumInventoryReportHandler()
            ->findCurriculumInventoryReportsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['curriculumInventoryReports'] =
            $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a CurriculumInventoryReport.
     *
     * @ApiDoc(
     *   section = "CurriculumInventoryReport",
     *   description = "Create a CurriculumInventoryReport.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CurriculumInventoryReportType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryReport",
     *   statusCodes={
     *     201 = "Created CurriculumInventoryReport.",
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
            $handler = $this->getCurriculumInventoryReportHandler();

            $curriculumInventoryReport = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $curriculumInventoryReport)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getCurriculumInventoryReportHandler()->updateCurriculumInventoryReport(
                $curriculumInventoryReport,
                true,
                false
            );

            $answer['curriculumInventoryReports'] = [$curriculumInventoryReport];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a CurriculumInventoryReport.
     *
     * @ApiDoc(
     *   section = "CurriculumInventoryReport",
     *   description = "Update a CurriculumInventoryReport entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CurriculumInventoryReportType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryReport",
     *   statusCodes={
     *     200 = "Updated CurriculumInventoryReport.",
     *     201 = "Created CurriculumInventoryReport.",
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
            $curriculumInventoryReport = $this->getCurriculumInventoryReportHandler()
                ->findCurriculumInventoryReportBy(['id'=> $id]);
            if ($curriculumInventoryReport) {
                $code = Codes::HTTP_OK;
            } else {
                $curriculumInventoryReport = $this->getCurriculumInventoryReportHandler()
                    ->createCurriculumInventoryReport();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->getCurriculumInventoryReportHandler();

            $curriculumInventoryReport = $handler->put(
                $curriculumInventoryReport,
                $this->getPostData($request)
            );

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $curriculumInventoryReport)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getCurriculumInventoryReportHandler()->updateCurriculumInventoryReport(
                $curriculumInventoryReport,
                true,
                true
            );

            $answer['curriculumInventoryReport'] = $curriculumInventoryReport;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a CurriculumInventoryReport.
     *
     * @ApiDoc(
     *   section = "CurriculumInventoryReport",
     *   description = "Delete a CurriculumInventoryReport entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "CurriculumInventoryReport identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted CurriculumInventoryReport.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal CurriculumInventoryReportInterface $curriculumInventoryReport
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $curriculumInventoryReport = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $curriculumInventoryReport)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $this->getCurriculumInventoryReportHandler()
                ->deleteCurriculumInventoryReport($curriculumInventoryReport);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return CurriculumInventoryReportInterface $curriculumInventoryReport
     */
    protected function getOr404($id)
    {
        $curriculumInventoryReport = $this->getCurriculumInventoryReportHandler()
            ->findCurriculumInventoryReportBy(['id' => $id]);
        if (!$curriculumInventoryReport) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $curriculumInventoryReport;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('curriculumInventoryReport')) {
            return $request->request->get('curriculumInventoryReport');
        }

        return $request->request->all();
    }

    /**
     * @return CurriculumInventoryReportHandler
     */
    protected function getCurriculumInventoryReportHandler()
    {
        return $this->container->get('ilioscore.curriculuminventoryreport.handler');
    }
}
