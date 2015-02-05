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
use Ilios\CoreBundle\Handler\ProgramYearStewardHandler;
use Ilios\CoreBundle\Entity\ProgramYearStewardInterface;

/**
 * ProgramYearSteward controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("ProgramYearSteward")
 */
class ProgramYearStewardController extends FOSRestController
{
    
    /**
     * Get a ProgramYearSteward
     *
     * @ApiDoc(
     *   description = "Get a ProgramYearSteward.",
     *   resource = true,
     *   requirements={
     *     {"name"="programYearStewardId", "dataType"="integer", "requirement"="", "description"="ProgramYearSteward identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\ProgramYearSteward",
     *   statusCodes={
     *     200 = "ProgramYearSteward.",
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
        $answer['programYearSteward'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all ProgramYearSteward.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all ProgramYearSteward.",
     *   output="Ilios\CoreBundle\Entity\ProgramYearSteward",
     *   statusCodes = {
     *     200 = "List of all ProgramYearSteward",
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

        $answer['programYearSteward'] =
            $this->getProgramYearStewardHandler()->findProgramYearStewardsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['programYearSteward']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a ProgramYearSteward.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a ProgramYearSteward.",
     *   input="Ilios\CoreBundle\Form\ProgramYearStewardType",
     *   output="Ilios\CoreBundle\Entity\ProgramYearSteward",
     *   statusCodes={
     *     201 = "Created ProgramYearSteward.",
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
            $new  =  $this->getProgramYearStewardHandler()->post($request->request->all());
            $answer['programYearSteward'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a ProgramYearSteward.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a ProgramYearSteward entity.",
     *   input="Ilios\CoreBundle\Form\ProgramYearStewardType",
     *   output="Ilios\CoreBundle\Entity\ProgramYearSteward",
     *   statusCodes={
     *     200 = "Updated ProgramYearSteward.",
     *     201 = "Created ProgramYearSteward.",
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
            if ($programYearSteward = $this->getProgramYearStewardHandler()->findProgramYearStewardBy(['programYearStewardId'=> $id])) {
                $answer['programYearSteward']= $this->getProgramYearStewardHandler()->put($programYearSteward, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['programYearSteward'] = $this->getProgramYearStewardHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a ProgramYearSteward.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a ProgramYearSteward.",
     *   input="Ilios\CoreBundle\Form\ProgramYearStewardType",
     *   output="Ilios\CoreBundle\Entity\ProgramYearSteward",
     *   requirements={
     *     {"name"="programYearStewardId", "dataType"="integer", "requirement"="", "description"="ProgramYearSteward identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated ProgramYearSteward.",
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
        $answer['programYearSteward'] = $this->getProgramYearStewardHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a ProgramYearSteward.
     *
     * @ApiDoc(
     *   description = "Delete a ProgramYearSteward entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "programYearStewardId",
     *         "dataType" = "integer",
     *         "requirement" = "",
     *         "description" = "ProgramYearSteward identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted ProgramYearSteward.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal ProgramYearStewardInterface $programYearSteward
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $programYearSteward = $this->getOr404($id);
        try {
            $this->getProgramYearStewardHandler()->deleteProgramYearSteward($programYearSteward);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return ProgramYearStewardInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getProgramYearStewardHandler()->findProgramYearStewardBy(['programYearStewardId' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $entity;
    }

    /**
     * @return ProgramYearStewardHandler
     */
    public function getProgramYearStewardHandler()
    {
        return $this->container->get('ilioscore.programyearsteward.handler');
    }
}
