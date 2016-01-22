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
use Ilios\CoreBundle\Handler\MeshDescriptorHandler;
use Ilios\CoreBundle\Entity\MeshDescriptorInterface;

/**
 * Class MeshDescriptorController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("MeshDescriptors")
 */
class MeshDescriptorController extends FOSRestController
{
    /**
     * Get a MeshDescriptor
     *
     * @ApiDoc(
     *   section = "MeshDescriptor",
     *   description = "Get a MeshDescriptor.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="MeshDescriptor identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\MeshDescriptor",
     *   statusCodes={
     *     200 = "MeshDescriptor.",
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
        $meshDescriptor = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $meshDescriptor)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['meshDescriptors'][] = $meshDescriptor;

        return $answer;
    }

    /**
     * Get all MeshDescriptor.
     *
     * @ApiDoc(
     *   section = "MeshDescriptor",
     *   description = "Get all MeshDescriptor.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\MeshDescriptor",
     *   statusCodes = {
     *     200 = "List of all MeshDescriptor",
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
     * @QueryParam(
     *   name="q",
     *   nullable=true,
     *   description="query for mesh descriptors with linked term, concept, etc"
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
        $q = !is_null($paramFetcher->get('q')) ? $paramFetcher->get('q') : false;
        $criteria = !is_null($paramFetcher->get('filters')) ? $paramFetcher->get('filters') : [];
        $criteria = array_map(function ($item) {
            $item = $item == 'null' ? null : $item;
            $item = $item == 'false' ? false : $item;
            $item = $item == 'true' ? true : $item;

            return $item;
        }, $criteria);
        if ($q) {
            $result = $this->getMeshDescriptorHandler()
                ->findMeshDescriptorsByQ(
                    $q,
                    $orderBy,
                    $limit,
                    $offset
                );
        } else {
            $result = $this->getMeshDescriptorHandler()
                ->findMeshDescriptorsBy(
                    $criteria,
                    $orderBy,
                    $limit,
                    $offset
                );
        }

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['meshDescriptors'] =
            $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a MeshDescriptor.
     *
     * @ApiDoc(
     *   section = "MeshDescriptor",
     *   description = "Create a MeshDescriptor.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\MeshDescriptorType",
     *   output="Ilios\CoreBundle\Entity\MeshDescriptor",
     *   statusCodes={
     *     201 = "Created MeshDescriptor.",
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
            $handler = $this->getMeshDescriptorHandler();

            $meshDescriptor = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $meshDescriptor)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getMeshDescriptorHandler()->updateMeshDescriptor($meshDescriptor, true, false);

            $answer['meshDescriptors'] = [$meshDescriptor];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a MeshDescriptor.
     *
     * @ApiDoc(
     *   section = "MeshDescriptor",
     *   description = "Update a MeshDescriptor entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\MeshDescriptorType",
     *   output="Ilios\CoreBundle\Entity\MeshDescriptor",
     *   statusCodes={
     *     200 = "Updated MeshDescriptor.",
     *     201 = "Created MeshDescriptor.",
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
            $meshDescriptor = $this->getMeshDescriptorHandler()
                ->findMeshDescriptorBy(['id'=> $id]);
            if ($meshDescriptor) {
                $code = Codes::HTTP_OK;
            } else {
                $meshDescriptor = $this->getMeshDescriptorHandler()
                    ->createMeshDescriptor();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->getMeshDescriptorHandler();

            $meshDescriptor = $handler->put(
                $meshDescriptor,
                $this->getPostData($request)
            );

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $meshDescriptor)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getMeshDescriptorHandler()->updateMeshDescriptor($meshDescriptor, true, true);

            $answer['meshDescriptor'] = $meshDescriptor;

        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a MeshDescriptor.
     *
     * @ApiDoc(
     *   section = "MeshDescriptor",
     *   description = "Delete a MeshDescriptor entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
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
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal MeshDescriptorInterface $meshDescriptor
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $meshDescriptor = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $meshDescriptor)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $this->getMeshDescriptorHandler()
                ->deleteMeshDescriptor($meshDescriptor);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return MeshDescriptorInterface $meshDescriptor
     */
    protected function getOr404($id)
    {
        $meshDescriptor = $this->getMeshDescriptorHandler()
            ->findMeshDescriptorBy(['id' => $id]);
        if (!$meshDescriptor) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $meshDescriptor;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('meshDescriptor')) {
            return $request->request->get('meshDescriptor');
        }

        return $request->request->all();
    }

    /**
     * @return MeshDescriptorHandler
     */
    protected function getMeshDescriptorHandler()
    {
        return $this->container->get('ilioscore.meshdescriptor.handler');
    }
}
