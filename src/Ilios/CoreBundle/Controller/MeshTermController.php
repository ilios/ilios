<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Handler\MeshTermHandler;
use Ilios\CoreBundle\Entity\MeshTermInterface;

/**
 * MeshTerm controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("MeshTerm")
 */
class MeshTermController extends FOSRestController
{

    /**
     * Get a MeshTerm
     *
     * @ApiDoc(
     *   description = "Get a MeshTerm.",
     *   resource = true,
     *   requirements={
     *     {"name"="meshTermUid", "dataType"="string", "requirement"="\w+", "description"="MeshTerm identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\MeshTerm",
     *   statusCodes={
     *     200 = "MeshTerm.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @View(serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return Response
     */
    public function getAction($id)
    {
        $answer['meshTerm'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all MeshTerm.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all MeshTerm.",
     *   output="Ilios\CoreBundle\Entity\MeshTerm",
     *   statusCodes = {
     *     200 = "List of all MeshTerm",
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

        $answer['meshTerm'] =
            $this->getMeshTermHandler()->findMeshTermsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['meshTerm']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a MeshTerm.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a MeshTerm.",
     *   input="Ilios\CoreBundle\Form\Type\MeshTermType",
     *   output="Ilios\CoreBundle\Entity\MeshTerm",
     *   statusCodes={
     *     201 = "Created MeshTerm.",
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
            $new  =  $this->getMeshTermHandler()->post($request->request->all());
            $answer['meshTerm'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a MeshTerm.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a MeshTerm entity.",
     *   input="Ilios\CoreBundle\Form\Type\MeshTermType",
     *   output="Ilios\CoreBundle\Entity\MeshTerm",
     *   statusCodes={
     *     200 = "Updated MeshTerm.",
     *     201 = "Created MeshTerm.",
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
            if ($meshTerm = $this->getMeshTermHandler()->findMeshTermBy(['meshTermUid'=> $id])) {
                $answer['meshTerm']= $this->getMeshTermHandler()->put($meshTerm, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['meshTerm'] = $this->getMeshTermHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a MeshTerm.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a MeshTerm.",
     *   input="Ilios\CoreBundle\Form\Type\MeshTermType",
     *   output="Ilios\CoreBundle\Entity\MeshTerm",
     *   requirements={
     *     {"name"="meshTermUid", "dataType"="string", "requirement"="\w+", "description"="MeshTerm identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated MeshTerm.",
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
        $answer['meshTerm'] = $this->getMeshTermHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a MeshTerm.
     *
     * @ApiDoc(
     *   description = "Delete a MeshTerm entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "meshTermUid",
     *         "dataType" = "string",
     *         "requirement" = "\w+",
     *         "description" = "MeshTerm identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted MeshTerm.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param $id
     * @internal MeshTermInterface $meshTerm
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $meshTerm = $this->getOr404($id);
        try {
            $this->getMeshTermHandler()->deleteMeshTerm($meshTerm);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return MeshTermInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getMeshTermHandler()->findMeshTermBy(['meshTermUid' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $entity;
    }

    /**
     * @return MeshTermHandler
     */
    protected function getMeshTermHandler()
    {
        return $this->container->get('ilioscore.meshterm.handler');
    }
}
