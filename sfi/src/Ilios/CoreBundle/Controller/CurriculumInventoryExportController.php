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
use Ilios\CoreBundle\Handler\CurriculumInventoryExportHandler;
use Ilios\CoreBundle\Entity\CurriculumInventoryExportInterface;

/**
 * CurriculumInventoryExport controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("CurriculumInventoryExport")
 */
class CurriculumInventoryExportController extends FOSRestController
{
    
    /**
     * Get a CurriculumInventoryExport
     *
     * @ApiDoc(
     *   description = "Get a CurriculumInventoryExport.",
     *   resource = true,
     *   requirements={
     *     {"name"="report", "dataType"="", "requirement"="", "description"="CurriculumInventoryExport identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryExport",
     *   statusCodes={
     *     200 = "CurriculumInventoryExport.",
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
        $answer['curriculumInventoryExport'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all CurriculumInventoryExport.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all CurriculumInventoryExport.",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryExport",
     *   statusCodes = {
     *     200 = "List of all CurriculumInventoryExport",
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

        $answer['curriculumInventoryExport'] =
            $this->getCurriculumInventoryExportHandler()->findCurriculumInventoryExportsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['curriculumInventoryExport']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a CurriculumInventoryExport.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a CurriculumInventoryExport.",
     *   input="Ilios\CoreBundle\Form\CurriculumInventoryExportType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryExport",
     *   statusCodes={
     *     201 = "Created CurriculumInventoryExport.",
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
            $new  =  $this->getCurriculumInventoryExportHandler()->post($request->request->all());
            $answer['curriculumInventoryExport'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a CurriculumInventoryExport.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a CurriculumInventoryExport entity.",
     *   input="Ilios\CoreBundle\Form\CurriculumInventoryExportType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryExport",
     *   statusCodes={
     *     200 = "Updated CurriculumInventoryExport.",
     *     201 = "Created CurriculumInventoryExport.",
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
            if ($curriculumInventoryExport = $this->getCurriculumInventoryExportHandler()->findCurriculumInventoryExportBy(['report'=> $id])) {
                $answer['curriculumInventoryExport']= $this->getCurriculumInventoryExportHandler()->put($curriculumInventoryExport, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['curriculumInventoryExport'] = $this->getCurriculumInventoryExportHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a CurriculumInventoryExport.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a CurriculumInventoryExport.",
     *   input="Ilios\CoreBundle\Form\CurriculumInventoryExportType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryExport",
     *   requirements={
     *     {"name"="report", "dataType"="", "requirement"="", "description"="CurriculumInventoryExport identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated CurriculumInventoryExport.",
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
        $answer['curriculumInventoryExport'] = $this->getCurriculumInventoryExportHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a CurriculumInventoryExport.
     *
     * @ApiDoc(
     *   description = "Delete a CurriculumInventoryExport entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "report",
     *         "dataType" = "",
     *         "requirement" = "",
     *         "description" = "CurriculumInventoryExport identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted CurriculumInventoryExport.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal CurriculumInventoryExportInterface $curriculumInventoryExport
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $curriculumInventoryExport = $this->getOr404($id);
        try {
            $this->getCurriculumInventoryExportHandler()->deleteCurriculumInventoryExport($curriculumInventoryExport);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return CurriculumInventoryExportInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getCurriculumInventoryExportHandler()->findCurriculumInventoryExportBy(['report' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $entity;
    }

    /**
     * @return CurriculumInventoryExportHandler
     */
    public function getCurriculumInventoryExportHandler()
    {
        return $this->container->get('ilioscore.curriculuminventoryexport.handler');
    }
}
