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
use Ilios\CoreBundle\Handler\MeshDescriptorHandler;
use Ilios\CoreBundle\Entity\MeshDescriptorInterface;

/**
 * MeshDescriptor controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("MeshDescriptor")
 */
class MeshDescriptorController extends FOSRestController
{
    
    /**
     * Get a MeshDescriptor
     *
     * @ApiDoc(
     *   description = "Get a MeshDescriptor.",
     *   resource = true,
     *   requirements={
     *     {"name"="id", "dataType"="string", "requirement"="\w+", "description"="MeshDescriptor identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\MeshDescriptor",
     *   statusCodes={
     *     200 = "MeshDescriptor.",
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
        $answer['meshDescriptor'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all MeshDescriptor.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all MeshDescriptor.",
     *   output="Ilios\CoreBundle\Entity\MeshDescriptor",
     *   statusCodes = {
     *     200 = "List of all MeshDescriptor",
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

        $answer['meshDescriptor'] =
            $this->getMeshDescriptorHandler()->findMeshDescriptorsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['meshDescriptor']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a MeshDescriptor.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a MeshDescriptor.",
     *   input="Ilios\CoreBundle\Form\MeshDescriptorType",
     *   output="Ilios\CoreBundle\Entity\MeshDescriptor",
     *   statusCodes={
     *     201 = "Created MeshDescriptor.",
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
            $new  =  $this->getMeshDescriptorHandler()->post($request->request->all());
            $answer['meshDescriptor'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a MeshDescriptor.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a MeshDescriptor entity.",
     *   input="Ilios\CoreBundle\Form\MeshDescriptorType",
     *   output="Ilios\CoreBundle\Entity\MeshDescriptor",
     *   statusCodes={
     *     200 = "Updated MeshDescriptor.",
     *     201 = "Created MeshDescriptor.",
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
            if ($meshDescriptor = $this->getMeshDescriptorHandler()->findMeshDescriptorBy(['id'=> $id])) {
                $answer['meshDescriptor']= $this->getMeshDescriptorHandler()->put($meshDescriptor, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['meshDescriptor'] = $this->getMeshDescriptorHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a MeshDescriptor.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a MeshDescriptor.",
     *   input="Ilios\CoreBundle\Form\MeshDescriptorType",
     *   output="Ilios\CoreBundle\Entity\MeshDescriptor",
     *   requirements={
     *     {"name"="id", "dataType"="string", "requirement"="\w+", "description"="MeshDescriptor identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated MeshDescriptor.",
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
        $answer['meshDescriptor'] = $this->getMeshDescriptorHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a MeshDescriptor.
     *
     * @ApiDoc(
     *   description = "Delete a MeshDescriptor entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "string",
     *         "requirement" = "\w+",
     *         "description" = "MeshDescriptor identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted MeshDescriptor.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal MeshDescriptorInterface $meshDescriptor
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $meshDescriptor = $this->getOr404($id);
        try {
            $this->getMeshDescriptorHandler()->deleteMeshDescriptor($meshDescriptor);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return MeshDescriptorInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getMeshDescriptorHandler()->findMeshDescriptorBy(['id' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $entity;
    }

    /**
     * @return MeshDescriptorHandler
     */
    public function getMeshDescriptorHandler()
    {
        return $this->container->get('ilioscore.meshdescriptor.handler');
    }
}
