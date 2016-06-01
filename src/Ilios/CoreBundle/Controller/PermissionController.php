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
use Ilios\CoreBundle\Entity\PermissionInterface;

/**
 * Class PermissionController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("Permissions")
 */
class PermissionController extends FOSRestController
{
    /**
     * Get a Permission
     *
     * @ApiDoc(
     *   section = "Permission",
     *   description = "Get a Permission.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="string",
     *        "requirement"="\w+",
     *        "description"="Permission identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\Permission",
     *   statusCodes={
     *     200 = "Permission.",
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
        $permission = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $permission)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['permissions'][] = $permission;

        return $answer;
    }

    /**
     * Get all Permission.
     *
     * @ApiDoc(
     *   section = "Permission",
     *   description = "Get all Permission.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\Permission",
     *   statusCodes = {
     *     200 = "List of all Permission",
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

        $manager = $this->container->get('ilioscore.permission.manager');
        $result = $manager->findBy($criteria, $orderBy, $limit, $offset);

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['permissions'] = $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a Permission.
     *
     * @ApiDoc(
     *   section = "Permission",
     *   description = "Create a Permission.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\PermissionType",
     *   output="Ilios\CoreBundle\Entity\Permission",
     *   statusCodes={
     *     201 = "Created Permission.",
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
            $handler = $this->container->get('ilioscore.permission.handler');
            $permission = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $permission)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager = $this->container->get('ilioscore.permission.manager');
            $manager->update($permission, true, false);

            $answer['permissions'] = [$permission];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a Permission.
     *
     * @ApiDoc(
     *   section = "Permission",
     *   description = "Update a Permission entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\PermissionType",
     *   output="Ilios\CoreBundle\Entity\Permission",
     *   statusCodes={
     *     200 = "Updated Permission.",
     *     201 = "Created Permission.",
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
            $manager = $this->container->get('ilioscore.permission.manager');
            $permission = $manager->findOneBy(['id'=> $id]);
            if ($permission) {
                $code = Codes::HTTP_OK;
            } else {
                $permission = $manager->create();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->container->get('ilioscore.permission.handler');
            $permission = $handler->put($permission, $this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $permission)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager->update($permission, true, true);

            $answer['permissions'] = $permission;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a Permission.
     *
     * @ApiDoc(
     *   section = "Permission",
     *   description = "Delete a Permission entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "string",
     *         "requirement" = "\w+",
     *         "description" = "Permission identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted Permission.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal PermissionInterface $permission
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $permission = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $permission)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $manager = $this->container->get('ilioscore.permission.manager');
            $manager->delete($permission);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return PermissionInterface $permission
     */
    protected function getOr404($id)
    {
        $manager = $this->container->get('ilioscore.permission.manager');
        $permission = $manager->findOneBy(['id' => $id]);
        if (!$permission) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $permission;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('permission')) {
            return $request->request->get('permission');
        }

        return $request->request->all();
    }
}
