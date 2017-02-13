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
use Ilios\CoreBundle\Entity\IlmSessionInterface;

/**
 * Class IlmSessionController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("IlmSessions")
 */
class IlmSessionController
{
    /**
     * Get a IlmSession
     *
     * @ApiDoc(
     *   section = "IlmSession",
     *   description = "Get a IlmSession.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="IlmSession identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\IlmSession",
     *   statusCodes={
     *     200 = "IlmSession.",
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
        $ilmSession = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $ilmSession)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['ilmSessions'][] = $ilmSession;

        return $answer;
    }

    /**
     * Get all IlmSession.
     *
     * @ApiDoc(
     *   section = "IlmSession",
     *   description = "Get all IlmSession.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\IlmSession",
     *   statusCodes = {
     *     200 = "List of all IlmSession",
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

        $manager = $this->container->get('ilioscore.ilmsession.manager');
        $result = $manager->findBy(
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
        $answer['ilmSessions'] =
            $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a IlmSession.
     *
     * @ApiDoc(
     *   section = "IlmSession",
     *   description = "Create a IlmSession.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\IlmSessionType",
     *   output="Ilios\CoreBundle\Entity\IlmSession",
     *   statusCodes={
     *     201 = "Created IlmSession.",
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
            $handler = $this->container->get('ilioscore.ilmsession.handler');
            $ilmSession = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $ilmSession)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager = $this->container->get('ilioscore.ilmsession.manager');
            $manager->update($ilmSession, true, false);

            $answer['ilmSessions'] = [$ilmSession];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a IlmSession.
     *
     * @ApiDoc(
     *   section = "IlmSession",
     *   description = "Update a IlmSession entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\IlmSessionType",
     *   output="Ilios\CoreBundle\Entity\IlmSession",
     *   statusCodes={
     *     200 = "Updated IlmSession.",
     *     201 = "Created IlmSession.",
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
            $manager = $this->container->get('ilioscore.ilmsession.manager');
            $ilmSession = $manager->findOneBy(['id'=> $id]);
            if ($ilmSession) {
                $code = Codes::HTTP_OK;
            } else {
                $ilmSession = $manager->create();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->container->get('ilioscore.ilmsession.handler');

            $ilmSession = $handler->put($ilmSession, $this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $ilmSession)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager->update($ilmSession, true, true);

            $answer['ilmSession'] = $ilmSession;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a IlmSession.
     *
     * @ApiDoc(
     *   section = "IlmSession",
     *   description = "Delete a IlmSession entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "IlmSession identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted IlmSession.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal IlmSessionInterface $ilmSession
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $ilmSession = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $ilmSession)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $manager = $this->container->get('ilioscore.ilmsession.manager');
            $manager->delete($ilmSession);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return IlmSessionInterface $ilmSession
     */
    protected function getOr404($id)
    {
        $manager = $this->container->get('ilioscore.ilmsession.manager');
        $ilmSession = $manager->findOneBy(['id' => $id]);
        if (!$ilmSession) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $ilmSession;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('ilmSession')) {
            return $request->request->get('ilmSession');
        }

        return $request->request->all();
    }
}
