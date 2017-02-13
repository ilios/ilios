<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use \Ilios\CoreBundle\Classes\AcademicYear;

/**
 * Educational Year controller.
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("AcademicYears")
 */
class AcademicYearController
{

    /**
     * Get the educational year,
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets an academic year",
     *   output = "Ilios\CoreBundle\Classes\AcademicYear",
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

        $answer['academicYears'] = [new AcademicYear($id)];

        return $answer;
    }

    /**
     * Get all AcademicYears.
     *
     * @ApiDoc(
     *   section = "AcademicYear",
     *   description = "Get all AcademicYears.",
     *   resource = true,
     *   output = "Ilios\CoreBundle\Classes\AcademicYear",
     *   statusCodes = {
     *     200 = "List of all AcademicYear",
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
     * @View(serializerEnableMaxDepthChecks=true)
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return Response
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $courseManager = $this->container->get('ilioscore.course.manager');
        $years = [];
        foreach ($courseManager->getYears() as $id) {
            $years[] = new AcademicYear($id);
        }
        $answer['academicYears'] = $years;

        return $answer;
    }
}
