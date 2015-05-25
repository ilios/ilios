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
use Ilios\CoreBundle\Handler\MeshUserSelectionHandler;
use Ilios\CoreBundle\Entity\MeshUserSelectionInterface;

/**
 * Class MeshUserSelectionController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("MeshUserSelections")
 */
class MeshUserSelectionController extends FOSRestController
{
    /**
     * Get a MeshUserSelection
     *
     * @ApiDoc(
     *   section = "MeshUserSelection",
     *   description = "Get a MeshUserSelection.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="MeshUserSelection identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\MeshUserSelection",
     *   statusCodes={
     *     200 = "MeshUserSelection.",
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
        $answer['meshUserSelection'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all MeshUserSelection.
     *
     * @ApiDoc(
     *   section = "MeshUserSelection",
     *   description = "Get all MeshUserSelection.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\MeshUserSelection",
     *   statusCodes = {
     *     200 = "List of all MeshUserSelection",
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

        $result = $this->getMeshUserSelectionHandler()
            ->findMeshUserSelectionsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        //If there are no matches return an empty array
        $answer['meshUserSelections'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a MeshUserSelection.
     *
     * @ApiDoc(
     *   section = "MeshUserSelection",
     *   description = "Create a MeshUserSelection.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\MeshUserSelectionType",
     *   output="Ilios\CoreBundle\Entity\MeshUserSelection",
     *   statusCodes={
     *     201 = "Created MeshUserSelection.",
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
            $meshuserselection = $this->getMeshUserSelectionHandler()
                ->post($this->getPostData($request));

            $response = new Response();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set(
                'Location',
                $this->generateUrl(
                    'get_meshuserselections',
                    ['id' => $meshuserselection->getId()],
                    true
                )
            );

            return $response;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a MeshUserSelection.
     *
     * @ApiDoc(
     *   section = "MeshUserSelection",
     *   description = "Update a MeshUserSelection entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\MeshUserSelectionType",
     *   output="Ilios\CoreBundle\Entity\MeshUserSelection",
     *   statusCodes={
     *     200 = "Updated MeshUserSelection.",
     *     201 = "Created MeshUserSelection.",
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
            $meshUserSelection = $this->getMeshUserSelectionHandler()
                ->findMeshUserSelectionBy(['id'=> $id]);
            if ($meshUserSelection) {
                $code = Codes::HTTP_OK;
            } else {
                $meshUserSelection = $this->getMeshUserSelectionHandler()->createMeshUserSelection();
                $code = Codes::HTTP_CREATED;
            }

            $answer['meshUserSelection'] =
                $this->getMeshUserSelectionHandler()->put(
                    $meshUserSelection,
                    $this->getPostData($request)
                );
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a MeshUserSelection.
     *
     * @ApiDoc(
     *   section = "MeshUserSelection",
     *   description = "Partial Update to a MeshUserSelection.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\MeshUserSelectionType",
     *   output="Ilios\CoreBundle\Entity\MeshUserSelection",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="\d+",
     *         "description"="MeshUserSelection identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated MeshUserSelection.",
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
        $answer['meshUserSelection'] =
            $this->getMeshUserSelectionHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a MeshUserSelection.
     *
     * @ApiDoc(
     *   section = "MeshUserSelection",
     *   description = "Delete a MeshUserSelection entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "MeshUserSelection identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted MeshUserSelection.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal MeshUserSelectionInterface $meshUserSelection
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $meshUserSelection = $this->getOr404($id);

        try {
            $this->getMeshUserSelectionHandler()->deleteMeshUserSelection($meshUserSelection);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return MeshUserSelectionInterface $meshUserSelection
     */
    protected function getOr404($id)
    {
        $meshUserSelection = $this->getMeshUserSelectionHandler()
            ->findMeshUserSelectionBy(['id' => $id]);
        if (!$meshUserSelection) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $meshUserSelection;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('meshUserSelection');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return MeshUserSelectionHandler
     */
    protected function getMeshUserSelectionHandler()
    {
        return $this->container->get('ilioscore.meshuserselection.handler');
    }
}
