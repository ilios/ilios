<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use \Ilios\CoreBundle\Classes\EducationalYear;

/**
 * Educational Year controller.
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("EducationalYear")
 */
class EducationalYearController extends FOSRestController
{

    /**
     * Get the educational year,
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a educational year",
     *   output = "Ilios\CoreBundle\Classes\CurrentSession",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the year is not found"
     *   }
     * )
     *
     *
     *
     * @param Request $request
     * @param $id
     *
     * @return Response
     */
    public function getAction(Request $request, $id)
    {
        $courseManager = $this->container->get('ilioscore.course.manager');
        $years = $courseManager->getYears();
        if (!in_array($id, $years)) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        $answer['educationalYear'] = new EducationalYear($id);

        return $answer;
    }

    /**
     * Get all EducationalYears.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all AamcMethod.",
     *   output="Ilios\CoreBundle\Classes\EducationalYear",
     *   statusCodes = {
     *     200 = "List of all EducationalYears",
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
     *   description="Offset from which to start listing years."
     * )
     * @QueryParam(
     *   name="limit",
     *   requirements="\d+",
     *   default="20",
     *   description="How many years to return."
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

        $courseManager = $this->container->get('ilioscore.course.manager');
        $years = [];
        foreach ($courseManager->getYears() as $id) {
            $years[] = new EducationalYear($id);
        }
        $answer['educationalYears'] = $years;

        return $answer;
    }
}
