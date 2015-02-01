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
use Ilios\CoreBundle\Handler\AamcPcrsHandler;
use Ilios\CoreBundle\Entity\AamcPcrsInterface;

/**
 * AamcPcrs controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("AamcPcrs")
 */
class AamcPcrsController extends FOSRestController
{
    
    /**
     * Get a AamcPcrs
     *
     * @ApiDoc(
     *   description = "Get a AamcPcrs.",
     *   resource = true,
     *   requirements={
     *     {"name"="pcrsId", "dataType"="string", "requirement"="\w+", "description"="AamcPcrs identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\AamcPcrs",
     *   statusCodes={
     *     200 = "AamcPcrs.",
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
        $answer['aamcPcrs'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all AamcPcrs.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all AamcPcrs.",
     *   output="Ilios\CoreBundle\Entity\AamcPcrs",
     *   statusCodes = {
     *     200 = "List of all AamcPcrs",
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

        $answer['aamcPcrs'] =
            $this->getAamcPcrsHandler()->findAamcPcrsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['aamcPcrs']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a AamcPcrs.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a AamcPcrs.",
     *   input="Ilios\CoreBundle\Form\AamcPcrsType",
     *   output="Ilios\CoreBundle\Entity\AamcPcrs",
     *   statusCodes={
     *     201 = "Created AamcPcrs.",
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
            $new  =  $this->getAamcPcrsHandler()->post($request->request->all());
            $answer['aamcPcrs'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a AamcPcrs.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a AamcPcrs entity.",
     *   input="Ilios\CoreBundle\Form\AamcPcrsType",
     *   output="Ilios\CoreBundle\Entity\AamcPcrs",
     *   statusCodes={
     *     200 = "Updated AamcPcrs.",
     *     201 = "Created AamcPcrs.",
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
            if ($aamcPcrs = $this->getAamcPcrsHandler()->findAamcPcrsBy(['pcrsId'=> $id])) {
                $answer['aamcPcrs']= $this->getAamcPcrsHandler()->put($aamcPcrs, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['aamcPcrs'] = $this->getAamcPcrsHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a AamcPcrs.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a AamcPcrs.",
     *   input="Ilios\CoreBundle\Form\AamcPcrsType",
     *   output="Ilios\CoreBundle\Entity\AamcPcrs",
     *   requirements={
     *     {"name"="pcrsId", "dataType"="string", "requirement"="\w+", "description"="AamcPcrs identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated AamcPcrs.",
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
        $answer['aamcPcrs'] = $this->getAamcPcrsHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a AamcPcrs.
     *
     * @ApiDoc(
     *   description = "Delete a AamcPcrs entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "pcrsId",
     *         "dataType" = "string",
     *         "requirement" = "\w+",
     *         "description" = "AamcPcrs identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted AamcPcrs.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal AamcPcrsInterface $aamcPcrs
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $aamcPcrs = $this->getOr404($id);
        try {
            $this->getAamcPcrsHandler()->deleteAamcPcrs($aamcPcrs);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return AamcPcrsInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getAamcPcrsHandler()->findAamcPcrsBy(['pcrsId' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $entity;
    }

    /**
     * @return AamcPcrsHandler
     */
    public function getAamcPcrsHandler()
    {
        return $this->container->get('ilioscore.aamcpcrs.handler');
    }
}
