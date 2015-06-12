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
use Ilios\CoreBundle\Handler\MeshConceptHandler;
use Ilios\CoreBundle\Entity\MeshConceptInterface;

/**
 * Class MeshConceptController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("MeshConcepts")
 */
class MeshConceptController extends FOSRestController
{
    /**
     * Get a MeshConcept
     *
     * @ApiDoc(
     *   section = "MeshConcept",
     *   description = "Get a MeshConcept.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="string",
     *        "requirement"="\w+",
     *        "description"="MeshConcept identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\MeshConcept",
     *   statusCodes={
     *     200 = "MeshConcept.",
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
        $answer['meshConcepts'][] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all MeshConcept.
     *
     * @ApiDoc(
     *   section = "MeshConcept",
     *   description = "Get all MeshConcept.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\MeshConcept",
     *   statusCodes = {
     *     200 = "List of all MeshConcept",
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

        $result = $this->getMeshConceptHandler()
            ->findMeshConceptsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        //If there are no matches return an empty array
        $answer['meshConcepts'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a MeshConcept.
     *
     * @ApiDoc(
     *   section = "MeshConcept",
     *   description = "Create a MeshConcept.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\MeshConceptType",
     *   output="Ilios\CoreBundle\Entity\MeshConcept",
     *   statusCodes={
     *     201 = "Created MeshConcept.",
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
            $meshconcept = $this->getMeshConceptHandler()
                ->post($this->getPostData($request));

            $response = new Response();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set(
                'Location',
                $this->generateUrl(
                    'get_meshconcepts',
                    ['id' => $meshconcept->getId()],
                    true
                )
            );

            return $response;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a MeshConcept.
     *
     * @ApiDoc(
     *   section = "MeshConcept",
     *   description = "Update a MeshConcept entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\MeshConceptType",
     *   output="Ilios\CoreBundle\Entity\MeshConcept",
     *   statusCodes={
     *     200 = "Updated MeshConcept.",
     *     201 = "Created MeshConcept.",
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
            $meshConcept = $this->getMeshConceptHandler()
                ->findMeshConceptBy(['id'=> $id]);
            if ($meshConcept) {
                $code = Codes::HTTP_OK;
            } else {
                $meshConcept = $this->getMeshConceptHandler()
                    ->createMeshConcept();
                $code = Codes::HTTP_CREATED;
            }

            $answer['meshConcept'] =
                $this->getMeshConceptHandler()->put(
                    $meshConcept,
                    $this->getPostData($request)
                );
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
     *   section = "MeshConcept",
     *   description = "Partial Update to a MeshConcept.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\MeshConceptType",
     *   output="Ilios\CoreBundle\Entity\MeshConcept",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="string",
     *         "requirement"="\w+",
     *         "description"="MeshConcept identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated MeshConcept.",
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
        $answer['meshConcept'] =
            $this->getMeshConceptHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a MeshConcept.
     *
     * @ApiDoc(
     *   section = "MeshConcept",
     *   description = "Delete a MeshConcept entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
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
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal MeshConceptInterface $meshConcept
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $meshConcept = $this->getOr404($id);

        try {
            $this->getMeshConceptHandler()
                ->deleteMeshConcept($meshConcept);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return MeshConceptInterface $meshConcept
     */
    protected function getOr404($id)
    {
        $meshConcept = $this->getMeshConceptHandler()
            ->findMeshConceptBy(['id' => $id]);
        if (!$meshConcept) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $meshConcept;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('meshConcept');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return MeshConceptHandler
     */
    protected function getMeshConceptHandler()
    {
        return $this->container->get('ilioscore.meshconcept.handler');
    }
}
