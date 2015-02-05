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
use Ilios\CoreBundle\Handler\MeshQualifierHandler;
use Ilios\CoreBundle\Entity\MeshQualifierInterface;

/**
 * MeshQualifier controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("MeshQualifier")
 */
class MeshQualifierController extends FOSRestController
{
    
    /**
     * Get a MeshQualifier
     *
     * @ApiDoc(
     *   description = "Get a MeshQualifier.",
     *   resource = true,
     *   requirements={
     *     {"name"="meshQualifierUid", "dataType"="string", "requirement"="\w+", "description"="MeshQualifier identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\MeshQualifier",
     *   statusCodes={
     *     200 = "MeshQualifier.",
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
        $answer['meshQualifier'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all MeshQualifier.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all MeshQualifier.",
     *   output="Ilios\CoreBundle\Entity\MeshQualifier",
     *   statusCodes = {
     *     200 = "List of all MeshQualifier",
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

        $answer['meshQualifier'] =
            $this->getMeshQualifierHandler()->findMeshQualifiersBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['meshQualifier']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a MeshQualifier.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a MeshQualifier.",
     *   input="Ilios\CoreBundle\Form\MeshQualifierType",
     *   output="Ilios\CoreBundle\Entity\MeshQualifier",
     *   statusCodes={
     *     201 = "Created MeshQualifier.",
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
            $new  =  $this->getMeshQualifierHandler()->post($request->request->all());
            $answer['meshQualifier'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a MeshQualifier.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a MeshQualifier entity.",
     *   input="Ilios\CoreBundle\Form\MeshQualifierType",
     *   output="Ilios\CoreBundle\Entity\MeshQualifier",
     *   statusCodes={
     *     200 = "Updated MeshQualifier.",
     *     201 = "Created MeshQualifier.",
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
            if ($meshQualifier = $this->getMeshQualifierHandler()->findMeshQualifierBy(['meshQualifierUid'=> $id])) {
                $answer['meshQualifier']= $this->getMeshQualifierHandler()->put($meshQualifier, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['meshQualifier'] = $this->getMeshQualifierHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a MeshQualifier.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a MeshQualifier.",
     *   input="Ilios\CoreBundle\Form\MeshQualifierType",
     *   output="Ilios\CoreBundle\Entity\MeshQualifier",
     *   requirements={
     *     {"name"="meshQualifierUid", "dataType"="string", "requirement"="\w+", "description"="MeshQualifier identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated MeshQualifier.",
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
        $answer['meshQualifier'] = $this->getMeshQualifierHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a MeshQualifier.
     *
     * @ApiDoc(
     *   description = "Delete a MeshQualifier entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "meshQualifierUid",
     *         "dataType" = "string",
     *         "requirement" = "\w+",
     *         "description" = "MeshQualifier identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted MeshQualifier.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal MeshQualifierInterface $meshQualifier
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $meshQualifier = $this->getOr404($id);
        try {
            $this->getMeshQualifierHandler()->deleteMeshQualifier($meshQualifier);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return MeshQualifierInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getMeshQualifierHandler()->findMeshQualifierBy(['meshQualifierUid' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $entity;
    }

    /**
     * @return MeshQualifierHandler
     */
    public function getMeshQualifierHandler()
    {
        return $this->container->get('ilioscore.meshqualifier.handler');
    }
}
