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
use Ilios\CoreBundle\Handler\AamcMethodHandler;
use Ilios\CoreBundle\Entity\AamcMethodInterface;

/**
 * Class AamcMethodController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("AamcMethods")
 */
class AamcMethodController extends FOSRestController
{
    /**
     * Get a AamcMethod
     *
     * @ApiDoc(
     *   section = "AamcMethod",
     *   description = "Get a AamcMethod.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="string",
     *        "requirement"="\w+",
     *        "description"="AamcMethod identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\AamcMethod",
     *   statusCodes={
     *     200 = "AamcMethod.",
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
        $answer['aamcMethod'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all AamcMethod.
     *
     * @ApiDoc(
     *   section = "AamcMethod",
     *   description = "Get all AamcMethod.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\AamcMethod",
     *   statusCodes = {
     *     200 = "List of all AamcMethod",
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

        $result = $this->getAamcMethodHandler()
            ->findAamcMethodsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        //If there are no matches return an empty array
        $answer['aamcMethods'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a AamcMethod.
     *
     * @ApiDoc(
     *   section = "AamcMethod",
     *   description = "Create a AamcMethod.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\AamcMethodType",
     *   output="Ilios\CoreBundle\Entity\AamcMethod",
     *   statusCodes={
     *     201 = "Created AamcMethod.",
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
            $aamcmethod = $this->getAamcMethodHandler()
                ->post($this->getPostData($request));

            $response = new Response();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set(
                'Location',
                $this->generateUrl(
                    'get_aamcmethods',
                    ['id' => $aamcmethod->getId()],
                    true
                )
            );

            return $response;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a AamcMethod.
     *
     * @ApiDoc(
     *   section = "AamcMethod",
     *   description = "Update a AamcMethod entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\AamcMethodType",
     *   output="Ilios\CoreBundle\Entity\AamcMethod",
     *   statusCodes={
     *     200 = "Updated AamcMethod.",
     *     201 = "Created AamcMethod.",
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
            $aamcMethod = $this->getAamcMethodHandler()
                ->findAamcMethodBy(['id'=> $id]);
            if ($aamcMethod) {
                $code = Codes::HTTP_OK;
            } else {
                $aamcMethod = $this->getAamcMethodHandler()->createAamcMethod();
                $code = Codes::HTTP_CREATED;
            }

            $answer['aamcMethod'] =
                $this->getAamcMethodHandler()->put(
                    $aamcMethod,
                    $this->getPostData($request)
                );
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
     *   section = "AamcMethod",
     *   description = "Partial Update to a AamcMethod.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\AamcMethodType",
     *   output="Ilios\CoreBundle\Entity\AamcMethod",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="string",
     *         "requirement"="\w+",
     *         "description"="AamcMethod identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated AamcMethod.",
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
        $answer['aamcMethod'] =
            $this->getAamcMethodHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a AamcMethod.
     *
     * @ApiDoc(
     *   section = "AamcMethod",
     *   description = "Delete a AamcMethod entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
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
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal AamcMethodInterface $aamcMethod
     *
     * @return Response
     */
    public function deleteAction($id)
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
     * @return AamcMethodInterface $aamcMethod
     */
    protected function getOr404($id)
    {
        $aamcMethod = $this->getAamcMethodHandler()
            ->findAamcMethodBy(['id' => $id]);
        if (!$aamcMethod) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $aamcMethod;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('aamcMethod');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return AamcMethodHandler
     */
    protected function getAamcMethodHandler()
    {
        return $this->container->get('ilioscore.aamcmethod.handler');
    }
}
