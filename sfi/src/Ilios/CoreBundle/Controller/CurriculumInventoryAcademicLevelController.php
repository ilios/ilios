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
use Ilios\CoreBundle\Handler\CurriculumInventoryAcademicLevelHandler;
use Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevelInterface;

/**
 * CurriculumInventoryAcademicLevel controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("CurriculumInventoryAcademicLevel")
 */
class CurriculumInventoryAcademicLevelController extends FOSRestController
{
    
    /**
     * Get a CurriculumInventoryAcademicLevel
     *
     * @ApiDoc(
     *   description = "Get a CurriculumInventoryAcademicLevel.",
     *   resource = true,
     *   requirements={
     *     {"name"="academicLevelId", "dataType"="integer", "requirement"="", "description"="CurriculumInventoryAcademicLevel identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel",
     *   statusCodes={
     *     200 = "CurriculumInventoryAcademicLevel.",
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
        $answer['curriculumInventoryAcademicLevel'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all CurriculumInventoryAcademicLevel.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all CurriculumInventoryAcademicLevel.",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel",
     *   statusCodes = {
     *     200 = "List of all CurriculumInventoryAcademicLevel",
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

        $answer['curriculumInventoryAcademicLevel'] =
            $this->getCurriculumInventoryAcademicLevelHandler()->findCurriculumInventoryAcademicLevelsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['curriculumInventoryAcademicLevel']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a CurriculumInventoryAcademicLevel.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a CurriculumInventoryAcademicLevel.",
     *   input="Ilios\CoreBundle\Form\CurriculumInventoryAcademicLevelType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel",
     *   statusCodes={
     *     201 = "Created CurriculumInventoryAcademicLevel.",
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
            $new  =  $this->getCurriculumInventoryAcademicLevelHandler()->post($request->request->all());
            $answer['curriculumInventoryAcademicLevel'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a CurriculumInventoryAcademicLevel.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a CurriculumInventoryAcademicLevel entity.",
     *   input="Ilios\CoreBundle\Form\CurriculumInventoryAcademicLevelType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel",
     *   statusCodes={
     *     200 = "Updated CurriculumInventoryAcademicLevel.",
     *     201 = "Created CurriculumInventoryAcademicLevel.",
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
            if ($curriculumInventoryAcademicLevel = $this->getCurriculumInventoryAcademicLevelHandler()->findCurriculumInventoryAcademicLevelBy(['academicLevelId'=> $id])) {
                $answer['curriculumInventoryAcademicLevel']= $this->getCurriculumInventoryAcademicLevelHandler()->put($curriculumInventoryAcademicLevel, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['curriculumInventoryAcademicLevel'] = $this->getCurriculumInventoryAcademicLevelHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a CurriculumInventoryAcademicLevel.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a CurriculumInventoryAcademicLevel.",
     *   input="Ilios\CoreBundle\Form\CurriculumInventoryAcademicLevelType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel",
     *   requirements={
     *     {"name"="academicLevelId", "dataType"="integer", "requirement"="", "description"="CurriculumInventoryAcademicLevel identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated CurriculumInventoryAcademicLevel.",
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
        $answer['curriculumInventoryAcademicLevel'] = $this->getCurriculumInventoryAcademicLevelHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a CurriculumInventoryAcademicLevel.
     *
     * @ApiDoc(
     *   description = "Delete a CurriculumInventoryAcademicLevel entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "academicLevelId",
     *         "dataType" = "integer",
     *         "requirement" = "",
     *         "description" = "CurriculumInventoryAcademicLevel identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted CurriculumInventoryAcademicLevel.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $curriculumInventoryAcademicLevel = $this->getOr404($id);
        try {
            $this->getCurriculumInventoryAcademicLevelHandler()->deleteCurriculumInventoryAcademicLevel($curriculumInventoryAcademicLevel);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return CurriculumInventoryAcademicLevelInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getCurriculumInventoryAcademicLevelHandler()->findCurriculumInventoryAcademicLevelBy(['academicLevelId' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $entity;
    }

    /**
     * @return CurriculumInventoryAcademicLevelHandler
     */
    public function getCurriculumInventoryAcademicLevelHandler()
    {
        return $this->container->get('ilioscore.curriculuminventoryacademiclevel.handler');
    }
}
