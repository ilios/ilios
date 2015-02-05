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
use Ilios\CoreBundle\Handler\IlmSessionFacetHandler;
use Ilios\CoreBundle\Entity\IlmSessionFacetInterface;

/**
 * IlmSessionFacet controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("IlmSession")
 */
class IlmSessionFacetController extends FOSRestController
{

    /**
     * Get a IlmSessionFacet
     *
     * @ApiDoc(
     *   description = "Get a IlmSessionFacet.",
     *   resource = true,
     *   requirements={
     *     {"name"="ilmSessionFacetId", "dataType"="integer", "requirement"="", "description"="IlmSessionFacet identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\IlmSessionFacet",
     *   statusCodes={
     *     200 = "IlmSessionFacet.",
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
        $answer['ilmSession'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all IlmSessionFacet.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all IlmSessionFacet.",
     *   output="Ilios\CoreBundle\Entity\IlmSessionFacet",
     *   statusCodes = {
     *     200 = "List of all IlmSessionFacet",
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

        $answer['ilmSessions'] =
            $this->getIlmSessionFacetHandler()->findIlmSessionFacetsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['ilmSessions']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a IlmSessionFacet.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a IlmSessionFacet.",
     *   input="Ilios\CoreBundle\Form\IlmSessionFacetType",
     *   output="Ilios\CoreBundle\Entity\IlmSessionFacet",
     *   statusCodes={
     *     201 = "Created IlmSessionFacet.",
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
            $new  =  $this->getIlmSessionFacetHandler()->post($request->request->all());
            $answer['ilmSession'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a IlmSessionFacet.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a IlmSessionFacet entity.",
     *   input="Ilios\CoreBundle\Form\IlmSessionFacetType",
     *   output="Ilios\CoreBundle\Entity\IlmSessionFacet",
     *   statusCodes={
     *     200 = "Updated IlmSessionFacet.",
     *     201 = "Created IlmSessionFacet.",
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
            if ($ilmSessionFacet = $this->getIlmSessionFacetHandler()->findIlmSessionFacetBy(['ilmSessionFacetId'=> $id])) {
                $answer['ilmSession']= $this->getIlmSessionFacetHandler()->put($ilmSessionFacet, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['ilmSession'] = $this->getIlmSessionFacetHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a IlmSessionFacet.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a IlmSessionFacet.",
     *   input="Ilios\CoreBundle\Form\IlmSessionFacetType",
     *   output="Ilios\CoreBundle\Entity\IlmSessionFacet",
     *   requirements={
     *     {"name"="ilmSessionFacetId", "dataType"="integer", "requirement"="", "description"="IlmSessionFacet identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated IlmSessionFacet.",
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
        $answer['ilmSession'] = $this->getIlmSessionFacetHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a IlmSessionFacet.
     *
     * @ApiDoc(
     *   description = "Delete a IlmSessionFacet entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "ilmSessionFacetId",
     *         "dataType" = "integer",
     *         "requirement" = "",
     *         "description" = "IlmSessionFacet identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted IlmSessionFacet.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal IlmSessionFacetInterface $ilmSessionFacet
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $ilmSessionFacet = $this->getOr404($id);
        try {
            $this->getIlmSessionFacetHandler()->deleteIlmSessionFacet($ilmSessionFacet);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return IlmSessionFacetInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getIlmSessionFacetHandler()->findIlmSessionFacetBy(['ilmSessionFacetId' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $entity;
    }

    /**
     * @return IlmSessionFacetHandler
     */
    public function getIlmSessionFacetHandler()
    {
        return $this->container->get('ilioscore.ilmsessionfacet.handler');
    }
}
