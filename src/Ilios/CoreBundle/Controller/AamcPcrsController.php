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
use Ilios\CoreBundle\Handler\AamcPcrsHandler;
use Ilios\CoreBundle\Entity\AamcPcrsInterface;

/**
 * Class AamcPcrsController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("AamcPcrs")
 */
class AamcPcrsController extends FOSRestController
{
    /**
     * Get a AamcPcrs
     *
     * @ApiDoc(
     *   section = "AamcPcrs",
     *   description = "Get a AamcPcrs.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="string",
     *        "requirement"="\w+",
     *        "description"="AamcPcrs identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\AamcPcrs",
     *   statusCodes={
     *     200 = "AamcPcrs.",
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
        $answer['aamcPcrs'][] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all AamcPcrs.
     *
     * @ApiDoc(
     *   section = "AamcPcrs",
     *   description = "Get all AamcPcrs.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\AamcPcrs",
     *   statusCodes = {
     *     200 = "List of all AamcPcrs",
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

        $result = $this->getAamcPcrsHandler()
            ->findAamcPcrsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        //If there are no matches return an empty array
        $answer['aamcPcrs'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a AamcPcrs.
     *
     * @ApiDoc(
     *   section = "AamcPcrs",
     *   description = "Create a AamcPcrs.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\AamcPcrsType",
     *   output="Ilios\CoreBundle\Entity\AamcPcrs",
     *   statusCodes={
     *     201 = "Created AamcPcrs.",
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
            $new  =  $this->getAamcPcrsHandler()
                ->post($this->getPostData($request));
            $answer['aamcPcrs'] = [$new];

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a AamcPcrs.
     *
     * @ApiDoc(
     *   section = "AamcPcrs",
     *   description = "Update a AamcPcrs entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\AamcPcrsType",
     *   output="Ilios\CoreBundle\Entity\AamcPcrs",
     *   statusCodes={
     *     200 = "Updated AamcPcrs.",
     *     201 = "Created AamcPcrs.",
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
            $aamcPcrs = $this->getAamcPcrsHandler()
                ->findAamcPcrsBy(['id'=> $id]);
            if ($aamcPcrs) {
                $code = Codes::HTTP_OK;
            } else {
                $aamcPcrs = $this->getAamcPcrsHandler()
                    ->createAamcPcrs();
                $code = Codes::HTTP_CREATED;
            }

            $answer['aamcPcrs'] =
                $this->getAamcPcrsHandler()->put(
                    $aamcPcrs,
                    $this->getPostData($request)
                );
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a AamcPcrs.
     *
     * @ApiDoc(
     *   section = "AamcPcrs",
     *   description = "Partial Update to a AamcPcrs.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\AamcPcrsType",
     *   output="Ilios\CoreBundle\Entity\AamcPcrs",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="string",
     *         "requirement"="\w+",
     *         "description"="AamcPcrs identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated AamcPcrs.",
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
        $answer['aamcPcrs'] =
            $this->getAamcPcrsHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a AamcPcrs.
     *
     * @ApiDoc(
     *   section = "AamcPcrs",
     *   description = "Delete a AamcPcrs entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "string",
     *         "requirement" = "\w+",
     *         "description" = "AamcPcrs identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted AamcPcrs.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal AamcPcrsInterface $aamcPcrs
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $aamcPcrs = $this->getOr404($id);

        try {
            $this->getAamcPcrsHandler()
                ->deleteAamcPcrs($aamcPcrs);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return AamcPcrsInterface $aamcPcrs
     */
    protected function getOr404($id)
    {
        $aamcPcrs = $this->getAamcPcrsHandler()
            ->findAamcPcrsBy(['id' => $id]);
        if (!$aamcPcrs) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $aamcPcrs;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('aamcPcrs');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return AamcPcrsHandler
     */
    protected function getAamcPcrsHandler()
    {
        return $this->container->get('ilioscore.aamcpcrs.handler');
    }
}
