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
use Ilios\CoreBundle\Entity\MeshSemanticTypeInterface;

/**
 * Class MeshSemanticTypeController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("MeshSemanticTypes")
 * @deprecated
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
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="MeshSemanticType identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\MeshSemanticType",
     *   statusCodes={
     *     200 = "MeshSemanticType.",
     *     404 = "Not Found."
     *   },
     *   deprecated = true
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
        $meshSemanticType = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $meshSemanticType)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['meshSemanticTypes'][] = $meshSemanticType;

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
     *   },
     *   deprecated = true
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
     * @QueryParam(
     *   name="q",
     *   nullable=true,
     *   description="query for mesh semanticTypes with linked term, concept, etc"
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

        $manager = $this->container->get('ilioscore.meshsemantictype.manager');
        $result = $manager->findBy($criteria, $orderBy, $limit, $offset);
        
        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['meshSemanticTypes'] =
            $result ? array_values($result) : [];

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
     *   },
     *   deprecated = true
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
            $handler = $this->container->get('ilioscore.meshsemantictype.handler');

            $meshSemanticType = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $meshSemanticType)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager = $this->container->get('ilioscore.meshsemantictype.manager');
            $manager->update($meshSemanticType, true, false);

            $answer['meshSemanticTypes'] = [$meshSemanticType];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
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
     *   },
     *   deprecated = true
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
            $manager = $this->container->get('ilioscore.meshsemantictype.manager');
            $meshSemanticType = $manager->findOneBy(['id'=> $id]);
            if ($meshSemanticType) {
                $code = Codes::HTTP_OK;
            } else {
                $meshSemanticType = $manager->create();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->container->get('ilioscore.meshsemantictype.handler');

            $meshSemanticType = $handler->put($meshSemanticType, $this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $meshSemanticType)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager = $this->container->get('ilioscore.meshsemantictype.manager');
            $manager->update($meshSemanticType, true, true);

            $answer['meshSemanticType'] = $meshSemanticType;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
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
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "MeshSemanticType identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted MeshSemanticType.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   },
     *   deprecated = true
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

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $meshSemanticType)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $manager = $this->container->get('ilioscore.meshsemantictype.manager');
            $manager->delete($meshSemanticType);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
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
        $manager = $this->container->get('ilioscore.meshsemantictype.manager');
        $meshSemanticType = $manager->findOneBy(['id' => $id]);
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
        if ($request->request->has('meshSemanticType')) {
            return $request->request->get('meshSemanticType');
        }

        return $request->request->all();
    }
}
