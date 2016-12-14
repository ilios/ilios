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
use Ilios\CoreBundle\Entity\AamcResourceTypeInterface;

/**
 * Class AamcResourceTypeController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("AamcResourceTypes")
 */
class AamcResourceTypeController extends FOSRestController
{
    /**
     * Get a AamcResourceType
     *
     * @ApiDoc(
     *   section = "AamcResourceType",
     *   description = "Get an AAMC resource type.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="string",
     *        "requirement"="\w+",
     *        "description"="AAMC resource type identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\AamcResourceType",
     *   statusCodes={
     *     200 = "AAMC resource type.",
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
        $manager = $this->container->get('ilioscore.aamcresourcetype.manager');

        $aamcResourceType = $manager->findDTOBy(['id' => $id]);
        if (!$aamcResourceType) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $aamcResourceType)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['aamcResourceTypes'][] = $aamcResourceType;

        return $answer;
    }

    /**
     * Get all AAMC resource types.
     *
     * @ApiDoc(
     *   section = "AamcResourceType",
     *   description = "Get all AAMC resource types.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\AamcResourceType",
     *   statusCodes = {
     *     200 = "List of all AAMC resource types",
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

        $manager = $this->container->get('ilioscore.aamcresourcetype.manager');
        $result = $manager->findDTOsBy($criteria, $orderBy, $limit, $offset);

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['aamcResourceTypes'] =
            $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a AAMC resource type.
     *
     * @ApiDoc(
     *   section = "AamcResourceType",
     *   description = "Create an AAMC resource type.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\AamcResourceTypeType",
     *   output="Ilios\CoreBundle\Entity\AamcResourceType",
     *   statusCodes={
     *     201 = "Created AamcResourceType.",
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
            $handler = $this->container->get('ilioscore.aamcresourcetype.handler');
            $aamcResourceType = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $aamcResourceType)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager = $this->container->get('ilioscore.aamcresourcetype.manager');
            $manager->update($aamcResourceType, true, false);

            $answer['aamcResourceTypes'] = [$aamcResourceType];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update an AAMC resource type.
     *
     * @ApiDoc(
     *   section = "AamcResourceType",
     *   description = "Update a AAMC resource type entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\AamcResourceTypeType",
     *   output="Ilios\CoreBundle\Entity\AamcResourceType",
     *   statusCodes={
     *     200 = "Updated AAMC resource type.",
     *     201 = "Created AAMC resource type.",
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
            $manager = $this->container->get('ilioscore.aamcresourcetype.manager');
            $aamcResourceType = $manager->findOneBy(['id'=> $id]);
            if ($aamcResourceType) {
                $code = Codes::HTTP_OK;
            } else {
                $aamcResourceType = $manager->create();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->container->get('ilioscore.aamcresourcetype.handler');
            $aamcResourceType = $handler->put($aamcResourceType, $this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $aamcResourceType)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager->update($aamcResourceType, true, true);

            $answer['aamcResourceType'] = $aamcResourceType;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete an AAMC resource type.
     *
     * @ApiDoc(
     *   section = "AamcResourceType",
     *   description = "Delete an AAMC resource type entity.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name" = "id",
     *        "dataType"="string",
     *        "requirement"="\w+",
     *        "description" = "AAMC resource type identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted AAMC resource type.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal AamcResourceTypeInterface $aamcResourceType
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $aamcResourceType = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $aamcResourceType)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $manager = $this->container->get('ilioscore.aamcresourcetype.manager');
            $manager->delete($aamcResourceType);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return AamcResourceTypeInterface $aamcResourceType
     */
    protected function getOr404($id)
    {
        $manager = $this->container->get('ilioscore.aamcresourcetype.manager');
        $aamcResourceType = $manager->findOneBy(['id' => $id]);
        if (!$aamcResourceType) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $aamcResourceType;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('aamcResourceTypes')) {
            return $request->request->get('aamcResourceTypes');
        }

        return $request->request->all();
    }
}
