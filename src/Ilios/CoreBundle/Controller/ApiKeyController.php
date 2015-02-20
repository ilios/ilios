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
use Ilios\CoreBundle\Handler\ApiKeyHandler;
use Ilios\CoreBundle\Entity\ApiKeyInterface;

/**
 * ApiKey controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("ApiKey")
 */
class ApiKeyController extends FOSRestController
{

    /**
     * Get a ApiKey
     *
     * @ApiDoc(
     *   description = "Get a ApiKey.",
     *   resource = true,
     *   requirements={
     *     {"name"="user", "dataType"="", "requirement"="", "description"="ApiKey identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\ApiKey",
     *   statusCodes={
     *     200 = "ApiKey.",
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
        $answer['apiKey'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all ApiKey.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all ApiKey.",
     *   output="Ilios\CoreBundle\Entity\ApiKey",
     *   statusCodes = {
     *     200 = "List of all ApiKey",
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

        $answer['apiKey'] =
            $this->getApiKeyHandler()->findApiKeysBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['apiKey']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a ApiKey.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a ApiKey.",
     *   input="Ilios\CoreBundle\Form\ApiKeyType",
     *   output="Ilios\CoreBundle\Entity\ApiKey",
     *   statusCodes={
     *     201 = "Created ApiKey.",
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
            $new  =  $this->getApiKeyHandler()->post($request->request->all());
            $answer['apiKey'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a ApiKey.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a ApiKey entity.",
     *   input="Ilios\CoreBundle\Form\ApiKeyType",
     *   output="Ilios\CoreBundle\Entity\ApiKey",
     *   statusCodes={
     *     200 = "Updated ApiKey.",
     *     201 = "Created ApiKey.",
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
            if ($apiKey = $this->getApiKeyHandler()->findApiKeyBy(['user'=> $id])) {
                $answer['apiKey']= $this->getApiKeyHandler()->put($apiKey, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['apiKey'] = $this->getApiKeyHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a ApiKey.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a ApiKey.",
     *   input="Ilios\CoreBundle\Form\ApiKeyType",
     *   output="Ilios\CoreBundle\Entity\ApiKey",
     *   requirements={
     *     {"name"="user", "dataType"="", "requirement"="", "description"="ApiKey identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated ApiKey.",
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
        $answer['apiKey'] = $this->getApiKeyHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a ApiKey.
     *
     * @ApiDoc(
     *   description = "Delete a ApiKey entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "user",
     *         "dataType" = "",
     *         "requirement" = "",
     *         "description" = "ApiKey identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted ApiKey.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal ApiKeyInterface $apiKey
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $apiKey = $this->getOr404($id);
        try {
            $this->getApiKeyHandler()->deleteApiKey($apiKey);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return ApiKeyInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getApiKeyHandler()->findApiKeyBy(['user' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $entity;
    }

    /**
     * @return ApiKeyHandler
     */
    protected function getApiKeyHandler()
    {
        return $this->container->get('ilioscore.apikey.handler');
    }
}
