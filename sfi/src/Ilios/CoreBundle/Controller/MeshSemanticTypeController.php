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
use Ilios\CoreBundle\Handler\MeshSemanticTypeHandler;
use Ilios\CoreBundle\Entity\MeshSemanticTypeInterface;

/**
 * MeshSemanticType controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("MeshSemanticType")
 */
class MeshSemanticTypeController extends FOSRestController
{
    
    /**
     * Get a MeshSemanticType
     *
     * @ApiDoc(
     *   description = "Get a MeshSemanticType.",
     *   resource = true,
     *   requirements={
     *     {"name"="meshSemanticTypeUid", "dataType"="string", "requirement"="\w+", "description"="MeshSemanticType identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\MeshSemanticType",
     *   statusCodes={
     *     200 = "MeshSemanticType.",
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
        $answer['meshSemanticType'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all MeshSemanticType.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all MeshSemanticType.",
     *   output="Ilios\CoreBundle\Entity\MeshSemanticType",
     *   statusCodes = {
     *     200 = "List of all MeshSemanticType",
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

        $answer['meshSemanticType'] =
            $this->getMeshSemanticTypeHandler()->findMeshSemanticTypesBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['meshSemanticType']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a MeshSemanticType.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a MeshSemanticType.",
     *   input="Ilios\CoreBundle\Form\MeshSemanticTypeType",
     *   output="Ilios\CoreBundle\Entity\MeshSemanticType",
     *   statusCodes={
     *     201 = "Created MeshSemanticType.",
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
            $new  =  $this->getMeshSemanticTypeHandler()->post($request->request->all());
            $answer['meshSemanticType'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a MeshSemanticType.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a MeshSemanticType entity.",
     *   input="Ilios\CoreBundle\Form\MeshSemanticTypeType",
     *   output="Ilios\CoreBundle\Entity\MeshSemanticType",
     *   statusCodes={
     *     200 = "Updated MeshSemanticType.",
     *     201 = "Created MeshSemanticType.",
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
            if ($meshSemanticType = $this->getMeshSemanticTypeHandler()->findMeshSemanticTypeBy(['meshSemanticTypeUid'=> $id])) {
                $answer['meshSemanticType']= $this->getMeshSemanticTypeHandler()->put($meshSemanticType, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['meshSemanticType'] = $this->getMeshSemanticTypeHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a MeshSemanticType.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a MeshSemanticType.",
     *   input="Ilios\CoreBundle\Form\MeshSemanticTypeType",
     *   output="Ilios\CoreBundle\Entity\MeshSemanticType",
     *   requirements={
     *     {"name"="meshSemanticTypeUid", "dataType"="string", "requirement"="\w+", "description"="MeshSemanticType identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated MeshSemanticType.",
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
        $answer['meshSemanticType'] = $this->getMeshSemanticTypeHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a MeshSemanticType.
     *
     * @ApiDoc(
     *   description = "Delete a MeshSemanticType entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "meshSemanticTypeUid",
     *         "dataType" = "string",
     *         "requirement" = "\w+",
     *         "description" = "MeshSemanticType identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted MeshSemanticType.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal MeshSemanticTypeInterface $meshSemanticType
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $meshSemanticType = $this->getOr404($id);
        try {
            $this->getMeshSemanticTypeHandler()->deleteMeshSemanticType($meshSemanticType);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return MeshSemanticTypeInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getMeshSemanticTypeHandler()->findMeshSemanticTypeBy(['meshSemanticTypeUid' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $entity;
    }

    /**
     * @return MeshSemanticTypeHandler
     */
    public function getMeshSemanticTypeHandler()
    {
        return $this->container->get('ilioscore.meshsemantictype.handler');
    }
}
