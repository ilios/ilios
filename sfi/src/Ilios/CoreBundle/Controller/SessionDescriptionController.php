<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View as FOSView;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Handler\SessionDescriptionHandler;
use Ilios\CoreBundle\Entity\SessionDescriptionInterface;

/**
 * SessionDescription controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("SessionDescription")
 */
class SessionDescriptionController extends FOSRestController
{
    
    /**
     * Get a SessionDescription
     *
     * @ApiDoc(
     *   description = "Get a SessionDescription.",
     *   resource = true,
     *   requirements={
     *     {"name"="session", "dataType"="", "requirement"="", "description"="SessionDescription identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\SessionDescription",
     *   statusCodes={
     *     200 = "SessionDescription.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $id
     *
     * @return Response
     */
    public function getAction(Request $request, $id)
    {
        $answer['sessionDescription'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all SessionDescription.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all SessionDescription.",
     *   output="Ilios\CoreBundle\Entity\SessionDescription",
     *   statusCodes = {
     *     200 = "List of all SessionDescription",
     *     204 = "No content. Nothing to list."
     *   }
     * )
     *
     * @View(serializerEnableMaxDepthChecks=true)
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return Response
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
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        $orderBy = $paramFetcher->get('order_by');
        $criteria = !is_null($paramFetcher->get('filters')) ? $paramFetcher->get('filters') : array();

        $answer['sessionDescription'] =
            $this->getSessionDescriptionHandler()->findSessionDescriptionsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['sessionDescription']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a SessionDescription.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a SessionDescription.",
     *   input="Ilios\CoreBundle\Form\SessionDescriptionType",
     *   output="Ilios\CoreBundle\Entity\SessionDescription",
     *   statusCodes={
     *     201 = "Created SessionDescription.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @View(statusCode=201, serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     *
     * @return Response
     */
    public function postAction(Request $request)
    {
        try {
            $new  =  $this->getSessionDescriptionHandler()->post($request->request->all());
            $answer['sessionDescription'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a SessionDescription.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a SessionDescription entity.",
     *   input="Ilios\CoreBundle\Form\SessionDescriptionType",
     *   output="Ilios\CoreBundle\Entity\SessionDescription",
     *   statusCodes={
     *     200 = "Updated SessionDescription.",
     *     201 = "Created SessionDescription.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $entity
     *
     * @return Response
     */
    public function putAction(Request $request, $id)
    {
        try {
            if ($sessionDescription = $this->getSessionDescriptionHandler()->findSessionDescriptionBy(['session'=> $id])) {
                $answer['sessionDescription']= $this->getSessionDescriptionHandler()->put($sessionDescription, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['sessionDescription'] = $this->getSessionDescriptionHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a SessionDescription.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a SessionDescription.",
     *   input="Ilios\CoreBundle\Form\SessionDescriptionType",
     *   output="Ilios\CoreBundle\Entity\SessionDescription",
     *   requirements={
     *     {"name"="session", "dataType"="", "requirement"="", "description"="SessionDescription identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated SessionDescription.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     *
     * @View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $entity
     *
     * @return Response
     */
    public function patchAction(Request $request, $id)
    {
        $answer['sessionDescription'] = $this->getSessionDescriptionHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a SessionDescription.
     *
     * @ApiDoc(
     *   description = "Delete a SessionDescription entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "session",
     *         "dataType" = "",
     *         "requirement" = "",
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
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal SessionDescriptionInterface $sessionDescription
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $sessionDescription = $this->getOr404($id);
        try {
            $this->getSessionDescriptionHandler()->deleteSessionDescription($sessionDescription);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return SessionDescriptionInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getSessionDescriptionHandler()->findSessionDescriptionBy(['session' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $entity;
    }

    /**
     * @return SessionDescriptionHandler
     */
    public function getSessionDescriptionHandler()
    {
        return $this->container->get('ilioscore.sessiondescription.handler');
    }
}
