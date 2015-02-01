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
use Ilios\CoreBundle\Handler\IngestionExceptionHandler;
use Ilios\CoreBundle\Entity\IngestionExceptionInterface;

/**
 * IngestionException controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("IngestionException")
 */
class IngestionExceptionController extends FOSRestController
{
    
    /**
     * Get a IngestionException
     *
     * @ApiDoc(
     *   description = "Get a IngestionException.",
     *   resource = true,
     *   requirements={
     *     {"name"="user", "dataType"="", "requirement"="", "description"="IngestionException identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\IngestionException",
     *   statusCodes={
     *     200 = "IngestionException.",
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
        $answer['ingestionException'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all IngestionException.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all IngestionException.",
     *   output="Ilios\CoreBundle\Entity\IngestionException",
     *   statusCodes = {
     *     200 = "List of all IngestionException",
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

        $answer['ingestionException'] =
            $this->getIngestionExceptionHandler()->findIngestionExceptionsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['ingestionException']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a IngestionException.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a IngestionException.",
     *   input="Ilios\CoreBundle\Form\IngestionExceptionType",
     *   output="Ilios\CoreBundle\Entity\IngestionException",
     *   statusCodes={
     *     201 = "Created IngestionException.",
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
            $new  =  $this->getIngestionExceptionHandler()->post($request->request->all());
            $answer['ingestionException'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a IngestionException.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a IngestionException entity.",
     *   input="Ilios\CoreBundle\Form\IngestionExceptionType",
     *   output="Ilios\CoreBundle\Entity\IngestionException",
     *   statusCodes={
     *     200 = "Updated IngestionException.",
     *     201 = "Created IngestionException.",
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
            if ($ingestionException = $this->getIngestionExceptionHandler()->findIngestionExceptionBy(['user'=> $id])) {
                $answer['ingestionException']= $this->getIngestionExceptionHandler()->put($ingestionException, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['ingestionException'] = $this->getIngestionExceptionHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a IngestionException.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a IngestionException.",
     *   input="Ilios\CoreBundle\Form\IngestionExceptionType",
     *   output="Ilios\CoreBundle\Entity\IngestionException",
     *   requirements={
     *     {"name"="user", "dataType"="", "requirement"="", "description"="IngestionException identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated IngestionException.",
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
        $answer['ingestionException'] = $this->getIngestionExceptionHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a IngestionException.
     *
     * @ApiDoc(
     *   description = "Delete a IngestionException entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "user",
     *         "dataType" = "",
     *         "requirement" = "",
     *         "description" = "IngestionException identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted IngestionException.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal IngestionExceptionInterface $ingestionException
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $ingestionException = $this->getOr404($id);
        try {
            $this->getIngestionExceptionHandler()->deleteIngestionException($ingestionException);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return IngestionExceptionInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getIngestionExceptionHandler()->findIngestionExceptionBy(['user' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $entity;
    }

    /**
     * @return IngestionExceptionHandler
     */
    public function getIngestionExceptionHandler()
    {
        return $this->container->get('ilioscore.ingestionexception.handler');
    }
}
