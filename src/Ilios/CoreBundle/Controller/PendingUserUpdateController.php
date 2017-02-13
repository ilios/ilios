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
use Ilios\CoreBundle\Entity\PendingUserUpdateInterface;

/**
 * Class PendingUserUpdateController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("PendingUserUpdates")
 */
class PendingUserUpdateController
{
    /**
     * Get a PendingUserUpdate
     *
     * @ApiDoc(
     *   section = "PendingUserUpdate",
     *   description = "Get a PendingUserUpdate.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="PendingUserUpdate identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\PendingUserUpdate",
     *   statusCodes={
     *     200 = "PendingUserUpdate.",
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
        $pendingUserUpdate = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $pendingUserUpdate)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['pendingUserUpdates'][] = $pendingUserUpdate;

        return $answer;
    }

    /**
     * Get all PendingUserUpdate.
     *
     * @ApiDoc(
     *   section = "PendingUserUpdate",
     *   description = "Get all PendingUserUpdates.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\PendingUserUpdate",
     *   statusCodes = {
     *     200 = "List of all PendingUserUpdate",
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

        $manager = $this->container->get('ilioscore.pendinguserupdate.manager');
        $result = $manager->findBy($criteria, $orderBy, $limit, $offset);

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['pendingUserUpdates'] =
            $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a PendingUserUpdate.
     *
     * @ApiDoc(
     *   section = "PendingUserUpdate",
     *   description = "Create a PendingUserUpdate.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\PendingUserUpdateType",
     *   output="Ilios\CoreBundle\Entity\PendingUserUpdate",
     *   statusCodes={
     *     201 = "Created PendingUserUpdate.",
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
            $handler = $this->container->get('ilioscore.pendinguserupdate.handler');
            $pendingUserUpdate = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $pendingUserUpdate)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager = $this->container->get('ilioscore.pendinguserupdate.manager');
            $manager->update($pendingUserUpdate, true, false);

            $answer['pendingUserUpdates'] = [$pendingUserUpdate];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a PendingUserUpdate.
     *
     * @ApiDoc(
     *   section = "PendingUserUpdate",
     *   description = "Update a PendingUserUpdate.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\PendingUserUpdateType",
     *   output="Ilios\CoreBundle\Entity\PendingUserUpdate",
     *   statusCodes={
     *     200 = "Updated PendingUserUpdate.",
     *     201 = "Created PendingUserUpdate.",
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
            $manager = $this->container->get('ilioscore.pendinguserupdate.manager');
            $pendingUserUpdate = $manager->findOneBy(['id'=> $id]);

            if ($pendingUserUpdate) {
                $code = Codes::HTTP_OK;
            } else {
                $pendingUserUpdate = $manager->create();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->container->get('ilioscore.pendinguserupdate.handler');
            $pendingUserUpdate = $handler->put($pendingUserUpdate, $this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $pendingUserUpdate)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager->update($pendingUserUpdate, true, true);

            $answer['pendingUserUpdate'] = $pendingUserUpdate;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }


    /**
     * Delete a PendingUserUpdate.
     *
     * @ApiDoc(
     *   section = "PendingUserUpdate",
     *   description = "Delete a PendingUserUpdate.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "PendingUserUpdate identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted PendingUserUpdate.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal PendingUserUpdateInterface $pendingUserUpdate
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $pendingUserUpdate = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $pendingUserUpdate)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $manager = $this->container->get('ilioscore.pendinguserupdate.manager');
            $manager->delete($pendingUserUpdate);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return PendingUserUpdateInterface $pendingUserUpdate
     */
    protected function getOr404($id)
    {
        $manager = $this->container->get('ilioscore.pendinguserupdate.manager');
        $pendingUserUpdate = $manager->findOneBy(['id' => $id]);
        if (!$pendingUserUpdate) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $pendingUserUpdate;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('pendingUserUpdate')) {
            return $request->request->get('pendingUserUpdate');
        }

        return $request->request->all();
    }
}
