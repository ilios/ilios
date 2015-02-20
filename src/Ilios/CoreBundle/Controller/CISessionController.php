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
use Ilios\CoreBundle\Handler\CISessionHandler;
use Ilios\CoreBundle\Entity\CISessionInterface;

/**
 * CISession controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("CISession")
 */
class CISessionController extends FOSRestController
{

    /**
     * Get a CISession
     *
     * @ApiDoc(
     *   description = "Get a CISession.",
     *   resource = true,
     *   requirements={
     *     {"name"="id", "dataType"="string", "requirement"="\w+", "description"="CISession identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\CISession",
     *   statusCodes={
     *     200 = "CISession.",
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
        $answer['cISession'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all CISession.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all CISession.",
     *   output="Ilios\CoreBundle\Entity\CISession",
     *   statusCodes = {
     *     200 = "List of all CISession",
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

        $answer['cISession'] =
            $this->getCISessionHandler()->findCISessionsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['cISession']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a CISession.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a CISession.",
     *   input="Ilios\CoreBundle\Form\CISessionType",
     *   output="Ilios\CoreBundle\Entity\CISession",
     *   statusCodes={
     *     201 = "Created CISession.",
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
            $new  =  $this->getCISessionHandler()->post($request->request->all());
            $answer['cISession'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a CISession.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a CISession entity.",
     *   input="Ilios\CoreBundle\Form\CISessionType",
     *   output="Ilios\CoreBundle\Entity\CISession",
     *   statusCodes={
     *     200 = "Updated CISession.",
     *     201 = "Created CISession.",
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
            if ($cISession = $this->getCISessionHandler()->findCISessionBy(['id'=> $id])) {
                $answer['cISession']= $this->getCISessionHandler()->put($cISession, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['cISession'] = $this->getCISessionHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a CISession.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a CISession.",
     *   input="Ilios\CoreBundle\Form\CISessionType",
     *   output="Ilios\CoreBundle\Entity\CISession",
     *   requirements={
     *     {"name"="id", "dataType"="string", "requirement"="\w+", "description"="CISession identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated CISession.",
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
        $answer['cISession'] = $this->getCISessionHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a CISession.
     *
     * @ApiDoc(
     *   description = "Delete a CISession entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "string",
     *         "requirement" = "\w+",
     *         "description" = "CISession identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted CISession.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal CISessionInterface $cISession
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $cISession = $this->getOr404($id);
        try {
            $this->getCISessionHandler()->deleteCISession($cISession);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return CISessionInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getCISessionHandler()->findCISessionBy(['id' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $entity;
    }

    /**
     * @return CISessionHandler
     */
    protected function getCISessionHandler()
    {
        return $this->container->get('ilioscore.cisession.handler');
    }
}
