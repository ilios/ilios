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
use Ilios\CoreBundle\Handler\SessionDescriptionHandler;
use Ilios\CoreBundle\Entity\SessionDescriptionInterface;

/**
 * Class SessionDescriptionController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("SessionDescriptions")
 */
class SessionDescriptionController extends FOSRestController
{
    /**
     * Get a SessionDescription
     *
     * @ApiDoc(
     *   section = "SessionDescription",
     *   description = "Get a SessionDescription.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="SessionDescription identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\SessionDescription",
     *   statusCodes={
     *     200 = "SessionDescription.",
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
        $sessionDescription = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $sessionDescription)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['sessionDescriptions'][] = $sessionDescription;

        return $answer;
    }

    /**
     * Get all SessionDescription.
     *
     * @ApiDoc(
     *   section = "SessionDescription",
     *   description = "Get all SessionDescription.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\SessionDescription",
     *   statusCodes = {
     *     200 = "List of all SessionDescription",
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

        $result = $this->getSessionDescriptionHandler()
            ->findSessionDescriptionsBy(
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
        $answer['sessionDescriptions'] =
            $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a SessionDescription.
     *
     * @ApiDoc(
     *   section = "SessionDescription",
     *   description = "Create a SessionDescription.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\SessionDescriptionType",
     *   output="Ilios\CoreBundle\Entity\SessionDescription",
     *   statusCodes={
     *     201 = "Created SessionDescription.",
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
            $handler = $this->getSessionDescriptionHandler();

            $sessionDescription = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $sessionDescription)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getSessionDescriptionHandler()->updateSessionDescription($sessionDescription, true, false);

            $answer['sessionDescriptions'] = [$sessionDescription];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a SessionDescription.
     *
     * @ApiDoc(
     *   section = "SessionDescription",
     *   description = "Update a SessionDescription entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\SessionDescriptionType",
     *   output="Ilios\CoreBundle\Entity\SessionDescription",
     *   statusCodes={
     *     200 = "Updated SessionDescription.",
     *     201 = "Created SessionDescription.",
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
            $sessionDescription = $this->getSessionDescriptionHandler()
                ->findSessionDescriptionBy(['id'=> $id]);
            if ($sessionDescription) {
                $code = Codes::HTTP_OK;
            } else {
                $sessionDescription = $this->getSessionDescriptionHandler()
                    ->createSessionDescription();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->getSessionDescriptionHandler();

            $sessionDescription = $handler->put(
                $sessionDescription,
                $this->getPostData($request)
            );

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $sessionDescription)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getSessionDescriptionHandler()->updateSessionDescription($sessionDescription, true, true);

            $answer['sessionDescription'] = $sessionDescription;

        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a SessionDescription.
     *
     * @ApiDoc(
     *   section = "SessionDescription",
     *   description = "Delete a SessionDescription entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "SessionDescription identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted SessionDescription.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal SessionDescriptionInterface $sessionDescription
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $sessionDescription = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $sessionDescription)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $this->getSessionDescriptionHandler()
                ->deleteSessionDescription($sessionDescription);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return SessionDescriptionInterface $sessionDescription
     */
    protected function getOr404($id)
    {
        $sessionDescription = $this->getSessionDescriptionHandler()
            ->findSessionDescriptionBy(['id' => $id]);
        if (!$sessionDescription) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $sessionDescription;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('sessionDescription')) {
            return $request->request->get('sessionDescription');
        }

        return $request->request->all();
    }

    /**
     * @return SessionDescriptionHandler
     */
    protected function getSessionDescriptionHandler()
    {
        return $this->container->get('ilioscore.sessiondescription.handler');
    }
}
