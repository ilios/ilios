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
use Ilios\CoreBundle\Handler\AamcMethodHandler;
use Ilios\CoreBundle\Entity\AamcMethodInterface;

/**
 * AamcMethod controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("AamcMethod")
 */
class AamcMethodController extends FOSRestController
{

    /**
     * Get a AamcMethod
     *
     * @ApiDoc(
     *   description = "Get a AamcMethod.",
     *   resource = true,
     *   requirements={
     *     {"name"="methodId", "dataType"="string", "requirement"="\w+", "description"="AamcMethod identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\AamcMethod",
     *   statusCodes={
     *     200 = "AamcMethod.",
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
        $answer['aamcMethod'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all AamcMethod.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all AamcMethod.",
     *   output="Ilios\CoreBundle\Entity\AamcMethod",
     *   statusCodes = {
     *     200 = "List of all AamcMethod",
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

        $answer['aamcMethods'] =
            $this->getAamcMethodHandler()->findAamcMethodsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['aamcMethods']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a AamcMethod.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a AamcMethod.",
     *   input="Ilios\CoreBundle\Form\AamcMethodType",
     *   output="Ilios\CoreBundle\Entity\AamcMethod",
     *   statusCodes={
     *     201 = "Created AamcMethod.",
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
            $new  =  $this->getAamcMethodHandler()->post($request->request->all());
            $answer['aamcMethod'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a AamcMethod.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a AamcMethod entity.",
     *   input="Ilios\CoreBundle\Form\AamcMethodType",
     *   output="Ilios\CoreBundle\Entity\AamcMethod",
     *   statusCodes={
     *     200 = "Updated AamcMethod.",
     *     201 = "Created AamcMethod.",
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
            if ($aamcMethod = $this->getAamcMethodHandler()->findAamcMethodBy(['methodId'=> $id])) {
                $answer['aamcMethod']= $this->getAamcMethodHandler()->put($aamcMethod, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['aamcMethod'] = $this->getAamcMethodHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a AamcMethod.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a AamcMethod.",
     *   input="Ilios\CoreBundle\Form\AamcMethodType",
     *   output="Ilios\CoreBundle\Entity\AamcMethod",
     *   requirements={
     *     {"name"="methodId", "dataType"="string", "requirement"="\w+", "description"="AamcMethod identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated AamcMethod.",
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
        $answer['aamcMethod'] = $this->getAamcMethodHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a AamcMethod.
     *
     * @ApiDoc(
     *   description = "Delete a AamcMethod entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "methodId",
     *         "dataType" = "string",
     *         "requirement" = "\w+",
     *         "description" = "AamcMethod identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted AamcMethod.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal AamcMethodInterface $aamcMethod
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $aamcMethod = $this->getOr404($id);
        try {
            $this->getAamcMethodHandler()->deleteAamcMethod($aamcMethod);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return AamcMethodInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getAamcMethodHandler()->findAamcMethodBy(['methodId' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $entity;
    }

    /**
     * @return AamcMethodHandler
     */
    public function getAamcMethodHandler()
    {
        return $this->container->get('ilioscore.aamcmethod.handler');
    }
}
