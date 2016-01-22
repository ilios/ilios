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
use Ilios\CoreBundle\Handler\MeshTreeHandler;
use Ilios\CoreBundle\Entity\MeshTreeInterface;

/**
 * Class MeshTreeController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("MeshTrees")
 */
class MeshTreeController extends FOSRestController
{
    /**
     * Get a MeshTree
     *
     * @ApiDoc(
     *   section = "MeshTree",
     *   description = "Get a MeshTree.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="MeshTree identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\MeshTree",
     *   statusCodes={
     *     200 = "MeshTree.",
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
        $meshTree = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $meshTree)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['meshTrees'][] = $meshTree;

        return $answer;
    }

    /**
     * Get all MeshTree.
     *
     * @ApiDoc(
     *   section = "MeshTree",
     *   description = "Get all MeshTree.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\MeshTree",
     *   statusCodes = {
     *     200 = "List of all MeshTree",
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
        $result = $this->getMeshTreeHandler()
            ->findMeshTreesBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['meshTrees'] =
            $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a MeshTree.
     *
     * @ApiDoc(
     *   section = "MeshTree",
     *   description = "Create a MeshTree.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\MeshTreeType",
     *   output="Ilios\CoreBundle\Entity\MeshTree",
     *   statusCodes={
     *     201 = "Created MeshTree.",
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
            $handler = $this->getMeshTreeHandler();

            $meshTree = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $meshTree)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getMeshTreeHandler()->updateMeshTree($meshTree, true, false);

            $answer['meshTrees'] = [$meshTree];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a MeshTree.
     *
     * @ApiDoc(
     *   section = "MeshTree",
     *   description = "Update a MeshTree entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\MeshTreeType",
     *   output="Ilios\CoreBundle\Entity\MeshTree",
     *   statusCodes={
     *     200 = "Updated MeshTree.",
     *     201 = "Created MeshTree.",
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
            $meshTree = $this->getMeshTreeHandler()
                ->findMeshTreeBy(['id'=> $id]);
            if ($meshTree) {
                $code = Codes::HTTP_OK;
            } else {
                $meshTree = $this->getMeshTreeHandler()
                    ->createMeshTree();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->getMeshTreeHandler();

            $meshTree = $handler->put(
                $meshTree,
                $this->getPostData($request)
            );

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $meshTree)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getMeshTreeHandler()->updateMeshTree($meshTree, true, true);

            $answer['meshTree'] = $meshTree;

        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a MeshTree.
     *
     * @ApiDoc(
     *   section = "MeshTree",
     *   description = "Delete a MeshTree entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "MeshTree identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted MeshTree.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal MeshTreeInterface $meshTree
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $meshTree = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $meshTree)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $this->getMeshTreeHandler()
                ->deleteMeshTree($meshTree);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return MeshTreeInterface $meshTree
     */
    protected function getOr404($id)
    {
        $meshTree = $this->getMeshTreeHandler()
            ->findMeshTreeBy(['id' => $id]);
        if (!$meshTree) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $meshTree;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('meshTree')) {
            return $request->request->get('meshTree');
        }

        return $request->request->all();
    }

    /**
     * @return MeshTreeHandler
     */
    protected function getMeshTreeHandler()
    {
        return $this->container->get('ilioscore.meshtree.handler');
    }
}
