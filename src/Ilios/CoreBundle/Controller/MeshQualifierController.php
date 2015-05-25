<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Handler\MeshQualifierHandler;
use Ilios\CoreBundle\Entity\MeshQualifierInterface;

/**
 * Class MeshQualifierController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("MeshQualifiers")
 */
class MeshQualifierController extends FOSRestController
{
    /**
     * Get a MeshQualifier
     *
     * @ApiDoc(
     *   section = "MeshQualifier",
     *   description = "Get a MeshQualifier.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="string",
     *        "requirement"="\w+",
     *        "description"="MeshQualifier identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\MeshQualifier",
     *   statusCodes={
     *     200 = "MeshQualifier.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return Response
     */
    public function getAction($id)
    {
        $answer['meshQualifier'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all MeshQualifier.
     *
     * @ApiDoc(
     *   section = "MeshQualifier",
     *   description = "Get all MeshQualifier.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\MeshQualifier",
     *   statusCodes = {
     *     200 = "List of all MeshQualifier",
     *     204 = "No content. Nothing to list."
     *   }
     * )
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
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return Response
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        $orderBy = $paramFetcher->get('order_by');
        $criteria = !is_null($paramFetcher->get('filters')) ? $paramFetcher->get('filters') : [];
        $criteria = array_map(function ($item) {
            $item = $item == 'null' ? null : $item;
            $item = $item == 'false' ? false : $item;
            $item = $item == 'true' ? true : $item;

            return $item;
        }, $criteria);

        $result = $this->getMeshQualifierHandler()
            ->findMeshQualifiersBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        //If there are no matches return an empty array
        $answer['meshQualifiers'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a MeshQualifier.
     *
     * @ApiDoc(
     *   section = "MeshQualifier",
     *   description = "Create a MeshQualifier.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\MeshQualifierType",
     *   output="Ilios\CoreBundle\Entity\MeshQualifier",
     *   statusCodes={
     *     201 = "Created MeshQualifier.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(statusCode=201, serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     *
     * @return Response
     */
    public function postAction(Request $request)
    {
        try {
            $meshqualifier = $this->getMeshQualifierHandler()
                ->post($this->getPostData($request));

            $response = new Response();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set(
                'Location',
                $this->generateUrl(
                    'get_meshqualifiers',
                    ['id' => $meshqualifier->getId()],
                    true
                )
            );

            return $response;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a MeshQualifier.
     *
     * @ApiDoc(
     *   section = "MeshQualifier",
     *   description = "Update a MeshQualifier entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\MeshQualifierType",
     *   output="Ilios\CoreBundle\Entity\MeshQualifier",
     *   statusCodes={
     *     200 = "Updated MeshQualifier.",
     *     201 = "Created MeshQualifier.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $id
     *
     * @return Response
     */
    public function putAction(Request $request, $id)
    {
        try {
            $meshQualifier = $this->getMeshQualifierHandler()
                ->findMeshQualifierBy(['id'=> $id]);
            if ($meshQualifier) {
                $code = Codes::HTTP_OK;
            } else {
                $meshQualifier = $this->getMeshQualifierHandler()->createMeshQualifier();
                $code = Codes::HTTP_CREATED;
            }

            $answer['meshQualifier'] =
                $this->getMeshQualifierHandler()->put(
                    $meshQualifier,
                    $this->getPostData($request)
                );
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
     *   section = "MeshQualifier",
     *   description = "Partial Update to a MeshQualifier.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\MeshQualifierType",
     *   output="Ilios\CoreBundle\Entity\MeshQualifier",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="string",
     *         "requirement"="\w+",
     *         "description"="MeshQualifier identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated MeshQualifier.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $id
     *
     * @return Response
     */
    public function patchAction(Request $request, $id)
    {
        $answer['meshQualifier'] =
            $this->getMeshQualifierHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a MeshQualifier.
     *
     * @ApiDoc(
     *   section = "MeshQualifier",
     *   description = "Delete a MeshQualifier entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
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
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal MeshQualifierInterface $meshQualifier
     *
     * @return Response
     */
    public function deleteAction($id)
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
     * @return MeshQualifierInterface $meshQualifier
     */
    protected function getOr404($id)
    {
        $meshQualifier = $this->getMeshQualifierHandler()
            ->findMeshQualifierBy(['id' => $id]);
        if (!$meshQualifier) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $meshQualifier;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('meshQualifier');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return MeshQualifierHandler
     */
    protected function getMeshQualifierHandler()
    {
        return $this->container->get('ilioscore.meshqualifier.handler');
    }
}
