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
use Ilios\CoreBundle\Handler\MeshSemanticTypeHandler;
use Ilios\CoreBundle\Entity\MeshSemanticTypeInterface;

/**
 * Class MeshSemanticTypeController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("MeshSemanticTypes")
 */
class MeshSemanticTypeController extends FOSRestController
{
    /**
     * Get a MeshSemanticType
     *
     * @ApiDoc(
     *   section = "MeshSemanticType",
     *   description = "Get a MeshSemanticType.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="string",
     *        "requirement"="\w+",
     *        "description"="MeshSemanticType identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\MeshSemanticType",
     *   statusCodes={
     *     200 = "MeshSemanticType.",
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
        $answer['meshSemanticTypes'][] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all MeshSemanticType.
     *
     * @ApiDoc(
     *   section = "MeshSemanticType",
     *   description = "Get all MeshSemanticType.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\MeshSemanticType",
     *   statusCodes = {
     *     200 = "List of all MeshSemanticType",
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

        $result = $this->getMeshSemanticTypeHandler()
            ->findMeshSemanticTypesBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        //If there are no matches return an empty array
        $answer['meshSemanticTypes'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a MeshSemanticType.
     *
     * @ApiDoc(
     *   section = "MeshSemanticType",
     *   description = "Create a MeshSemanticType.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\MeshSemanticTypeType",
     *   output="Ilios\CoreBundle\Entity\MeshSemanticType",
     *   statusCodes={
     *     201 = "Created MeshSemanticType.",
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
            $new  =  $this->getMeshSemanticTypeHandler()
                ->post($this->getPostData($request));
            $answer['meshSemanticTypes'] = [$new];

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a MeshSemanticType.
     *
     * @ApiDoc(
     *   section = "MeshSemanticType",
     *   description = "Update a MeshSemanticType entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\MeshSemanticTypeType",
     *   output="Ilios\CoreBundle\Entity\MeshSemanticType",
     *   statusCodes={
     *     200 = "Updated MeshSemanticType.",
     *     201 = "Created MeshSemanticType.",
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
            $meshSemanticType = $this->getMeshSemanticTypeHandler()
                ->findMeshSemanticTypeBy(['id'=> $id]);
            if ($meshSemanticType) {
                $code = Codes::HTTP_OK;
            } else {
                $meshSemanticType = $this->getMeshSemanticTypeHandler()
                    ->createMeshSemanticType();
                $code = Codes::HTTP_CREATED;
            }

            $answer['meshSemanticType'] =
                $this->getMeshSemanticTypeHandler()->put(
                    $meshSemanticType,
                    $this->getPostData($request)
                );
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
     *   section = "MeshSemanticType",
     *   description = "Partial Update to a MeshSemanticType.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\MeshSemanticTypeType",
     *   output="Ilios\CoreBundle\Entity\MeshSemanticType",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="string",
     *         "requirement"="\w+",
     *         "description"="MeshSemanticType identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated MeshSemanticType.",
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
        $answer['meshSemanticType'] =
            $this->getMeshSemanticTypeHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a MeshSemanticType.
     *
     * @ApiDoc(
     *   section = "MeshSemanticType",
     *   description = "Delete a MeshSemanticType entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
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
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal MeshSemanticTypeInterface $meshSemanticType
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $meshSemanticType = $this->getOr404($id);

        try {
            $this->getMeshSemanticTypeHandler()
                ->deleteMeshSemanticType($meshSemanticType);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return MeshSemanticTypeInterface $meshSemanticType
     */
    protected function getOr404($id)
    {
        $meshSemanticType = $this->getMeshSemanticTypeHandler()
            ->findMeshSemanticTypeBy(['id' => $id]);
        if (!$meshSemanticType) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $meshSemanticType;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('meshSemanticType');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return MeshSemanticTypeHandler
     */
    protected function getMeshSemanticTypeHandler()
    {
        return $this->container->get('ilioscore.meshsemantictype.handler');
    }
}
