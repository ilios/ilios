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
use Ilios\CoreBundle\Handler\OfferingHandler;
use Ilios\CoreBundle\Entity\OfferingInterface;

/**
 * Offering controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("Offering")
 */
class OfferingController extends FOSRestController
{
    
    /**
     * Get a Offering
     *
     * @ApiDoc(
     *   description = "Get a Offering.",
     *   resource = true,
     *   requirements={
     *     {"name"="id", "dataType"="integer", "requirement"="", "description"="Offering identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\Offering",
     *   statusCodes={
     *     200 = "Offering.",
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
        $answer['offering'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all Offering.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all Offering.",
     *   output="Ilios\CoreBundle\Entity\Offering",
     *   statusCodes = {
     *     200 = "List of all Offering",
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

        $answer['offering'] =
            $this->getOfferingHandler()->findOfferingsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['offering']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a Offering.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a Offering.",
     *   input="Ilios\CoreBundle\Form\OfferingType",
     *   output="Ilios\CoreBundle\Entity\Offering",
     *   statusCodes={
     *     201 = "Created Offering.",
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
            $new  =  $this->getOfferingHandler()->post($request->request->all());
            $answer['offering'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a Offering.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a Offering entity.",
     *   input="Ilios\CoreBundle\Form\OfferingType",
     *   output="Ilios\CoreBundle\Entity\Offering",
     *   statusCodes={
     *     200 = "Updated Offering.",
     *     201 = "Created Offering.",
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
            if ($offering = $this->getOfferingHandler()->findOfferingBy(['id'=> $id])) {
                $answer['offering']= $this->getOfferingHandler()->put($offering, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['offering'] = $this->getOfferingHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a Offering.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a Offering.",
     *   input="Ilios\CoreBundle\Form\OfferingType",
     *   output="Ilios\CoreBundle\Entity\Offering",
     *   requirements={
     *     {"name"="id", "dataType"="integer", "requirement"="", "description"="Offering identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated Offering.",
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
        $answer['offering'] = $this->getOfferingHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a Offering.
     *
     * @ApiDoc(
     *   description = "Delete a Offering entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "",
     *         "description" = "Offering identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted Offering.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal OfferingInterface $offering
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $offering = $this->getOr404($id);
        try {
            $this->getOfferingHandler()->deleteOffering($offering);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return OfferingInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getOfferingHandler()->findOfferingBy(['id' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $entity;
    }

    /**
     * @return OfferingHandler
     */
    public function getOfferingHandler()
    {
        return $this->container->get('ilioscore.offering.handler');
    }
}
