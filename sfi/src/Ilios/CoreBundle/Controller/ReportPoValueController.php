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
use Ilios\CoreBundle\Handler\ReportPoValueHandler;
use Ilios\CoreBundle\Entity\ReportPoValueInterface;

/**
 * ReportPoValue controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("ReportPoValue")
 */
class ReportPoValueController extends FOSRestController
{
    
    /**
     * Get a ReportPoValue
     *
     * @ApiDoc(
     *   description = "Get a ReportPoValue.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="report",
     *        "dataType"="",
     *        "requirement"="",
     *        "description"="ReportPoValue identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\ReportPoValue",
     *   statusCodes={
     *     200 = "ReportPoValue.",
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
        $answer['reportPoValue'] = $this->getOr404($id);

        return $answer;
    }
    /**
     * Get all ReportPoValue.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all ReportPoValue.",
     *   output="Ilios\CoreBundle\Entity\ReportPoValue",
     *   statusCodes = {
     *     200 = "List of all ReportPoValue",
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

        $result = $this->getReportPoValueHandler()
            ->findReportPoValuesBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );
        //If there are no matches return an empty array
        $answer['reportPoValues'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a ReportPoValue.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a ReportPoValue.",
     *   input="Ilios\CoreBundle\Form\ReportPoValueType",
     *   output="Ilios\CoreBundle\Entity\ReportPoValue",
     *   statusCodes={
     *     201 = "Created ReportPoValue.",
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
            $new  =  $this->getReportPoValueHandler()->post($this->getPostData($request));
            $answer['reportPoValue'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a ReportPoValue.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a ReportPoValue entity.",
     *   input="Ilios\CoreBundle\Form\ReportPoValueType",
     *   output="Ilios\CoreBundle\Entity\ReportPoValue",
     *   statusCodes={
     *     200 = "Updated ReportPoValue.",
     *     201 = "Created ReportPoValue.",
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
            $reportPoValue = $this->getReportPoValueHandler()
                ->findReportPoValueBy(['report'=> $id]);
            if ($reportPoValue) {
                $answer['reportPoValue'] =
                    $this->getReportPoValueHandler()->put(
                        $reportPoValue,
                        $this->getPostData($request)
                    );
                $code = Codes::HTTP_OK;
            } else {
                $answer['reportPoValue'] =
                    $this->getReportPoValueHandler()->post($this->getPostData($request));
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a ReportPoValue.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a ReportPoValue.",
     *   input="Ilios\CoreBundle\Form\ReportPoValueType",
     *   output="Ilios\CoreBundle\Entity\ReportPoValue",
     *   requirements={
     *     {
     *         "name"="report",
     *         "dataType"="",
     *         "requirement"="",
     *         "description"="ReportPoValue identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated ReportPoValue.",
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
        $answer['reportPoValue'] =
            $this->getReportPoValueHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a ReportPoValue.
     *
     * @ApiDoc(
     *   description = "Delete a ReportPoValue entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "report",
     *         "dataType" = "",
     *         "requirement" = "",
     *         "description" = "ReportPoValue identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted ReportPoValue.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal ReportPoValueInterface $reportPoValue
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $reportPoValue = $this->getOr404($id);
        try {
            $this->getReportPoValueHandler()
                ->deleteReportPoValue($reportPoValue);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return ReportPoValueInterface $entity
     */
    protected function getOr404($id)
    {
        $entity = $this->getReportPoValueHandler()
            ->findReportPoValueBy(['report' => $id]);
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
        return $request->request->get('reportPoValue', array());
    }
    /**
     * @return ReportPoValueHandler
     */
    protected function getReportPoValueHandler()
    {
        return $this->container->get('ilioscore.reportpovalue.handler');
    }
}
