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
use Ilios\CoreBundle\Handler\CurriculumInventoryReportHandler;
use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;

/**
 * CurriculumInventoryReport controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("CurriculumInventoryReport")
 */
class CurriculumInventoryReportController extends FOSRestController
{
    
    /**
     * Get a CurriculumInventoryReport
     *
     * @ApiDoc(
     *   description = "Get a CurriculumInventoryReport.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="",
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
     * @View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $id
     *
     * @return Response
     */
    public function getAction(Request $request, $id)
    {
        $answer['curriculumInventoryReport'] = $this->getOr404($id);

        return $answer;
    }
    /**
     * Get all CurriculumInventoryReport.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all CurriculumInventoryReport.",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryReport",
     *   statusCodes = {
     *     200 = "List of all CurriculumInventoryReport",
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

        $criteria = array_map(function ($item) {
            $item = $item == 'null'?null:$item;
            $item = $item == 'false'?false:$item;
            $item = $item == 'true'?true:$item;
            return $item;
        }, $criteria);

        $result = $this->getCurriculumInventoryReportHandler()
            ->findCurriculumInventoryReportsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );
        //If there are no matches return an empty array
        $answer['curriculumInventoryReports'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a CurriculumInventoryReport.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a CurriculumInventoryReport.",
     *   input="Ilios\CoreBundle\Form\CurriculumInventoryReportType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryReport",
     *   statusCodes={
     *     201 = "Created CurriculumInventoryReport.",
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
            $new  =  $this->getCurriculumInventoryReportHandler()->post($this->getPostData($request));
            $answer['curriculumInventoryReport'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a CurriculumInventoryReport.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a CurriculumInventoryReport entity.",
     *   input="Ilios\CoreBundle\Form\CurriculumInventoryReportType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryReport",
     *   statusCodes={
     *     200 = "Updated CurriculumInventoryReport.",
     *     201 = "Created CurriculumInventoryReport.",
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
            $curriculumInventoryReport = $this->getCurriculumInventoryReportHandler()
                ->findCurriculumInventoryReportBy(['id'=> $id]);
            if ($curriculumInventoryReport) {
                $answer['curriculumInventoryReport'] =
                    $this->getCurriculumInventoryReportHandler()->put(
                        $curriculumInventoryReport,
                        $this->getPostData($request)
                    );
                $code = Codes::HTTP_OK;
            } else {
                $answer['curriculumInventoryReport'] =
                    $this->getCurriculumInventoryReportHandler()->post($this->getPostData($request));
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a CurriculumInventoryReport.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a CurriculumInventoryReport.",
     *   input="Ilios\CoreBundle\Form\CurriculumInventoryReportType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryReport",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="",
     *         "description"="CurriculumInventoryReport identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated CurriculumInventoryReport.",
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
        $answer['curriculumInventoryReport'] =
            $this->getCurriculumInventoryReportHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a CurriculumInventoryReport.
     *
     * @ApiDoc(
     *   description = "Delete a CurriculumInventoryReport entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "",
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
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal CurriculumInventoryReportInterface $curriculumInventoryReport
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $curriculumInventoryReport = $this->getOr404($id);
        try {
            $this->getCurriculumInventoryReportHandler()
                ->deleteCurriculumInventoryReport($curriculumInventoryReport);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return CurriculumInventoryReportInterface $entity
     */
    protected function getOr404($id)
    {
        $entity = $this->getCurriculumInventoryReportHandler()
            ->findCurriculumInventoryReportBy(['id' => $id]);
        if (!$entity) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $entity;
    }
   /**
    * Parse the request for the form data
    *
    * @param Request $request
    * @return array
     */
    protected function getPostData(Request $request)
    {
        return $request->request->get('curriculumInventoryReport', array());
    }
    /**
     * @return CurriculumInventoryReportHandler
     */
    protected function getCurriculumInventoryReportHandler()
    {
        return $this->container->get('ilioscore.curriculuminventoryreport.handler');
    }
}
