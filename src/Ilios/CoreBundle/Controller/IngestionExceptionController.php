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
use Ilios\CoreBundle\Handler\IngestionExceptionHandler;
use Ilios\CoreBundle\Entity\IngestionExceptionInterface;

/**
 * Class IngestionExceptionController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("IngestionExceptions")
 */
class IngestionExceptionController extends FOSRestController
{
    /**
     * Get a IngestionException
     *
     * @ApiDoc(
     *   section = "IngestionException",
     *   description = "Get a IngestionException.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="user",
     *        "dataType"="",
     *        "requirement"="",
     *        "description"="IngestionException identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\IngestionException",
     *   statusCodes={
     *     200 = "IngestionException.",
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
        $answer['ingestionExceptions'][] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all IngestionException.
     *
     * @ApiDoc(
     *   section = "IngestionException",
     *   description = "Get all IngestionException.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\IngestionException",
     *   statusCodes = {
     *     200 = "List of all IngestionException",
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

        $result = $this->getIngestionExceptionHandler()
            ->findIngestionExceptionsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        //If there are no matches return an empty array
        $answer['ingestionExceptions'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a IngestionException.
     *
     * @ApiDoc(
     *   section = "IngestionException",
     *   description = "Create a IngestionException.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\IngestionExceptionType",
     *   output="Ilios\CoreBundle\Entity\IngestionException",
     *   statusCodes={
     *     201 = "Created IngestionException.",
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
            $new  =  $this->getIngestionExceptionHandler()
                ->post($this->getPostData($request));
            $answer['ingestionExceptions'] = [$new];

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a IngestionException.
     *
     * @ApiDoc(
     *   section = "IngestionException",
     *   description = "Update a IngestionException entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\IngestionExceptionType",
     *   output="Ilios\CoreBundle\Entity\IngestionException",
     *   statusCodes={
     *     200 = "Updated IngestionException.",
     *     201 = "Created IngestionException.",
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
            $ingestionException = $this->getIngestionExceptionHandler()
                ->findIngestionExceptionBy(['user'=> $id]);
            if ($ingestionException) {
                $code = Codes::HTTP_OK;
            } else {
                $ingestionException = $this->getIngestionExceptionHandler()
                    ->createIngestionException();
                $code = Codes::HTTP_CREATED;
            }

            $answer['ingestionException'] =
                $this->getIngestionExceptionHandler()->put(
                    $ingestionException,
                    $this->getPostData($request)
                );
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
     *   section = "IngestionException",
     *   description = "Partial Update to a IngestionException.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\IngestionExceptionType",
     *   output="Ilios\CoreBundle\Entity\IngestionException",
     *   requirements={
     *     {
     *         "name"="user",
     *         "dataType"="",
     *         "requirement"="",
     *         "description"="IngestionException identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated IngestionException.",
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
    public function patchAction(Request $request, $id)
    {
        $answer['ingestionException'] =
            $this->getIngestionExceptionHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a IngestionException.
     *
     * @ApiDoc(
     *   section = "IngestionException",
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
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal IngestionExceptionInterface $ingestionException
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $ingestionException = $this->getOr404($id);

        try {
            $this->getIngestionExceptionHandler()
                ->deleteIngestionException($ingestionException);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return IngestionExceptionInterface $ingestionException
     */
    protected function getOr404($id)
    {
        $ingestionException = $this->getIngestionExceptionHandler()
            ->findIngestionExceptionBy(['user' => $id]);
        if (!$ingestionException) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $ingestionException;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('ingestionException');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return IngestionExceptionHandler
     */
    protected function getIngestionExceptionHandler()
    {
        return $this->container->get('ilioscore.ingestionexception.handler');
    }
}
