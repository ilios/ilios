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
use Ilios\CoreBundle\Entity\MeshConceptInterface;

/**
 * Class MeshConceptController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("MeshConcepts")
 */
class MeshConceptController
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
     *        "dataType"="integer",
     *        "requirement"="\d+",
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
        $meshConcept = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $meshConcept)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['meshConcepts'][] = $meshConcept;

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

        $manager = $this->container->get('ilioscore.meshconcept.manager');
        $result = $manager->findBy($criteria, $orderBy, $limit, $offset);

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['meshConcepts'] =
            $result ? array_values($result) : [];

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
            $handler = $this->container->get('ilioscore.meshconcept.handler');

            $meshConcept = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $meshConcept)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager = $this->container->get('ilioscore.meshconcept.manager');
            $manager->update($meshConcept, true, false);

            $answer['meshConcepts'] = [$meshConcept];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
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
            $manager = $this->container->get('ilioscore.meshconcept.manager');
            $meshConcept = $manager->findOneBy(['id'=> $id]);
            if ($meshConcept) {
                $code = Codes::HTTP_OK;
            } else {
                $meshConcept = $manager->create();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->container->get('ilioscore.meshconcept.handler');

            $meshConcept = $handler->put($meshConcept, $this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $meshConcept)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager->update($meshConcept, true, true);

            $answer['meshConcept'] = $meshConcept;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
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
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
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

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $meshConcept)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $manager = $this->container->get('ilioscore.meshconcept.manager');
            $manager->delete($meshConcept);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
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
        $manager = $this->container->get('ilioscore.meshconcept.manager');
        $meshConcept = $manager->findOneBy(['id' => $id]);
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
        if ($request->request->has('meshConcept')) {
            return $request->request->get('meshConcept');
        }

        return $request->request->all();
    }
}
