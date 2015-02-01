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
use Ilios\CoreBundle\Handler\MeshConceptHandler;
use Ilios\CoreBundle\Entity\MeshConceptInterface;

/**
 * MeshConcept controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("MeshConcept")
 */
class MeshConceptController extends FOSRestController
{
    
    /**
     * Get a MeshConcept
     *
     * @ApiDoc(
     *   description = "Get a MeshConcept.",
     *   resource = true,
     *   requirements={
     *     {"name"="meshConceptUid", "dataType"="string", "requirement"="\w+", "description"="MeshConcept identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\MeshConcept",
     *   statusCodes={
     *     200 = "MeshConcept.",
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
        $answer['meshConcept'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all MeshConcept.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all MeshConcept.",
     *   output="Ilios\CoreBundle\Entity\MeshConcept",
     *   statusCodes = {
     *     200 = "List of all MeshConcept",
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

        $answer['meshConcept'] =
            $this->getMeshConceptHandler()->findMeshConceptsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['meshConcept']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a MeshConcept.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a MeshConcept.",
     *   input="Ilios\CoreBundle\Form\MeshConceptType",
     *   output="Ilios\CoreBundle\Entity\MeshConcept",
     *   statusCodes={
     *     201 = "Created MeshConcept.",
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
            $new  =  $this->getMeshConceptHandler()->post($request->request->all());
            $answer['meshConcept'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a MeshConcept.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a MeshConcept entity.",
     *   input="Ilios\CoreBundle\Form\MeshConceptType",
     *   output="Ilios\CoreBundle\Entity\MeshConcept",
     *   statusCodes={
     *     200 = "Updated MeshConcept.",
     *     201 = "Created MeshConcept.",
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
            if ($meshConcept = $this->getMeshConceptHandler()->findMeshConceptBy(['meshConceptUid'=> $id])) {
                $answer['meshConcept']= $this->getMeshConceptHandler()->put($meshConcept, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['meshConcept'] = $this->getMeshConceptHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a MeshConcept.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a MeshConcept.",
     *   input="Ilios\CoreBundle\Form\MeshConceptType",
     *   output="Ilios\CoreBundle\Entity\MeshConcept",
     *   requirements={
     *     {"name"="meshConceptUid", "dataType"="string", "requirement"="\w+", "description"="MeshConcept identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated MeshConcept.",
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
        $answer['meshConcept'] = $this->getMeshConceptHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a MeshConcept.
     *
     * @ApiDoc(
     *   description = "Delete a MeshConcept entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "meshConceptUid",
     *         "dataType" = "string",
     *         "requirement" = "\w+",
     *         "description" = "MeshConcept identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted MeshConcept.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal MeshConceptInterface $meshConcept
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $meshConcept = $this->getOr404($id);
        try {
            $this->getMeshConceptHandler()->deleteMeshConcept($meshConcept);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return MeshConceptInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getMeshConceptHandler()->findMeshConceptBy(['meshConceptUid' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $entity;
    }

    /**
     * @return MeshConceptHandler
     */
    public function getMeshConceptHandler()
    {
        return $this->container->get('ilioscore.meshconcept.handler');
    }
}
