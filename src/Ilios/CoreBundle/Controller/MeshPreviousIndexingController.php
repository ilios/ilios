<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Entity\MeshPreviousIndexingInterface;

/**
 * Class MeshPreviousIndexingController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("MeshPreviousIndexings")
 */
class MeshPreviousIndexingController extends FOSRestController
{
    /**
     * Get a MeshPreviousIndexing
     *
     * @ApiDoc(
     *   section = "MeshPreviousIndexing",
     *   description = "Get a MeshPreviousIndexing.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="MeshPreviousIndexing identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\MeshPreviousIndexing",
     *   statusCodes={
     *     200 = "MeshPreviousIndexing.",
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
        $meshPreviousIndexing = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $meshPreviousIndexing)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['meshPreviousIndexings'][] = $meshPreviousIndexing;

        return $answer;
    }

    /**
     * Get all MeshPreviousIndexing.
     *
     * @ApiDoc(
     *   section = "MeshPreviousIndexing",
     *   description = "Get all MeshPreviousIndexing.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\MeshPreviousIndexing",
     *   statusCodes = {
     *     200 = "List of all MeshPreviousIndexing",
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
        
        $manager = $this->container->get('ilioscore.meshpreviousindexing.manager');
        $result = $manager->findBy($criteria, $orderBy, $limit, $offset);

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['meshPreviousIndexings'] = $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a MeshPreviousIndexing.
     *
     * @ApiDoc(
     *   section = "MeshPreviousIndexing",
     *   description = "Create a MeshPreviousIndexing.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\MeshPreviousIndexingType",
     *   output="Ilios\CoreBundle\Entity\MeshPreviousIndexing",
     *   statusCodes={
     *     201 = "Created MeshPreviousIndexing.",
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
            $handler = $this->container->get('ilioscore.meshpreviousindexing.handler');

            $meshPreviousIndexing = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $meshPreviousIndexing)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager = $this->container->get('ilioscore.meshpreviousindexing.manager');
            $manager->update($meshPreviousIndexing, true, false);

            $answer['meshPreviousIndexings'] = [$meshPreviousIndexing];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a MeshPreviousIndexing.
     *
     * @ApiDoc(
     *   section = "MeshPreviousIndexing",
     *   description = "Update a MeshPreviousIndexing entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\MeshPreviousIndexingType",
     *   output="Ilios\CoreBundle\Entity\MeshPreviousIndexing",
     *   statusCodes={
     *     200 = "Updated MeshPreviousIndexing.",
     *     201 = "Created MeshPreviousIndexing.",
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
            $manager = $this->container->get('ilioscore.meshpreviousindexing.manager');
            $meshPreviousIndexing = $manager->findOneBy(['id'=> $id]);
            if ($meshPreviousIndexing) {
                $code = Codes::HTTP_OK;
            } else {
                $meshPreviousIndexing = $manager->create();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->container->get('ilioscore.meshpreviousindexing.handler');

            $meshPreviousIndexing = $handler->put($meshPreviousIndexing, $this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $meshPreviousIndexing)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager->update($meshPreviousIndexing, true, true);

            $answer['meshPreviousIndexing'] = $meshPreviousIndexing;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a MeshPreviousIndexing.
     *
     * @ApiDoc(
     *   section = "MeshPreviousIndexing",
     *   description = "Delete a MeshPreviousIndexing entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "MeshPreviousIndexing identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted MeshPreviousIndexing.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal MeshPreviousIndexingInterface $meshPreviousIndexing
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $meshPreviousIndexing = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $meshPreviousIndexing)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $manager = $this->container->get('ilioscore.meshpreviousindexing.manager');
            $manager->delete($meshPreviousIndexing);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return MeshPreviousIndexingInterface $meshPreviousIndexing
     */
    protected function getOr404($id)
    {
        $manager = $this->container->get('ilioscore.meshpreviousindexing.manager');
        $meshPreviousIndexing = $manager->findOneBy(['id' => $id]);
        if (!$meshPreviousIndexing) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $meshPreviousIndexing;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('meshPreviousIndexing')) {
            return $request->request->get('meshPreviousIndexing');
        }

        return $request->request->all();
    }
}
