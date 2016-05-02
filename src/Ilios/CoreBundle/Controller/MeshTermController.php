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
use Ilios\CoreBundle\Handler\MeshTermHandler;
use Ilios\CoreBundle\Entity\MeshTermInterface;

/**
 * Class MeshTermController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("MeshTerms")
 */
class MeshTermController extends FOSRestController
{
    /**
     * Get a MeshTerm
     *
     * @ApiDoc(
     *   section = "MeshTerm",
     *   description = "Get a MeshTerm.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="MeshTerm identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\MeshTerm",
     *   statusCodes={
     *     200 = "MeshTerm.",
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
        $meshTerm = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $meshTerm)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['meshTerms'][] = $meshTerm;

        return $answer;
    }

    /**
     * Get all MeshTerm.
     *
     * @ApiDoc(
     *   section = "MeshTerm",
     *   description = "Get all MeshTerm.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\MeshTerm",
     *   statusCodes = {
     *     200 = "List of all MeshTerm",
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
        $result = $this->getMeshTermHandler()
            ->findMeshTermsBy(
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
        $answer['meshTerms'] =
            $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a MeshTerm.
     *
     * @ApiDoc(
     *   section = "MeshTerm",
     *   description = "Create a MeshTerm.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\MeshTermType",
     *   output="Ilios\CoreBundle\Entity\MeshTerm",
     *   statusCodes={
     *     201 = "Created MeshTerm.",
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
            $handler = $this->getMeshTermHandler();

            $meshTerm = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $meshTerm)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getMeshTermHandler()->updateMeshTerm($meshTerm, true, false);

            $answer['meshTerms'] = [$meshTerm];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a MeshTerm.
     *
     * @ApiDoc(
     *   section = "MeshTerm",
     *   description = "Update a MeshTerm entity.",
     *   resource = true,
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
            $meshTerm = $this->getMeshTermHandler()
                ->findMeshTermBy(['id'=> $id]);
            if ($meshTerm) {
                $code = Codes::HTTP_OK;
            } else {
                $meshTerm = $this->getMeshTermHandler()
                    ->createMeshTerm();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->getMeshTermHandler();

            $meshTerm = $handler->put(
                $meshTerm,
                $this->getPostData($request)
            );

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $meshTerm)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getMeshTermHandler()->updateMeshTerm($meshTerm, true, true);

            $answer['meshTerm'] = $meshTerm;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a MeshTerm.
     *
     * @ApiDoc(
     *   section = "MeshTerm",
     *   description = "Delete a MeshTerm entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
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
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal MeshTermInterface $meshTerm
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $meshTerm = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $meshTerm)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $this->getMeshTermHandler()
                ->deleteMeshTerm($meshTerm);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return MeshTermInterface $meshTerm
     */
    protected function getOr404($id)
    {
        $meshTerm = $this->getMeshTermHandler()
            ->findMeshTermBy(['id' => $id]);
        if (!$meshTerm) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $meshTerm;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('meshTerm')) {
            return $request->request->get('meshTerm');
        }

        return $request->request->all();
    }

    /**
     * @return MeshTermHandler
     */
    protected function getMeshTermHandler()
    {
        return $this->container->get('ilioscore.meshterm.handler');
    }
}
