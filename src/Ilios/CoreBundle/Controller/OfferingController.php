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
use Ilios\CoreBundle\Handler\OfferingHandler;
use Ilios\CoreBundle\Entity\OfferingInterface;

/**
 * Class OfferingController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("Offerings")
 */
class OfferingController extends FOSRestController
{
    /**
     * Get a Offering
     *
     * @ApiDoc(
     *   section = "Offering",
     *   description = "Get a Offering.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="Offering identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\Offering",
     *   statusCodes={
     *     200 = "Offering.",
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
        $answer['offerings'][] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all Offering.
     *
     * @ApiDoc(
     *   section = "Offering",
     *   description = "Get all Offering.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\Offering",
     *   statusCodes = {
     *     200 = "List of all Offering",
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

        $result = $this->getOfferingHandler()
            ->findOfferingsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        //If there are no matches return an empty array
        $answer['offerings'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a Offering.
     *
     * @ApiDoc(
     *   section = "Offering",
     *   description = "Create a Offering.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\OfferingType",
     *   output="Ilios\CoreBundle\Entity\Offering",
     *   statusCodes={
     *     201 = "Created Offering.",
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
            $offering = $this->getOfferingHandler()
                ->post($this->getPostData($request));

            $response = new Response();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set(
                'Location',
                $this->generateUrl(
                    'get_offerings',
                    ['id' => $offering->getId()],
                    true
                )
            );

            return $response;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a Offering.
     *
     * @ApiDoc(
     *   section = "Offering",
     *   description = "Update a Offering entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\OfferingType",
     *   output="Ilios\CoreBundle\Entity\Offering",
     *   statusCodes={
     *     200 = "Updated Offering.",
     *     201 = "Created Offering.",
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
            $offering = $this->getOfferingHandler()
                ->findOfferingBy(['id'=> $id]);
            if ($offering) {
                $code = Codes::HTTP_OK;
            } else {
                $offering = $this->getOfferingHandler()
                    ->createOffering();
                $code = Codes::HTTP_CREATED;
            }

            $answer['offering'] =
                $this->getOfferingHandler()->put(
                    $offering,
                    $this->getPostData($request)
                );
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a Offering.
     *
     * @ApiDoc(
     *   section = "Offering",
     *   description = "Partial Update to a Offering.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\OfferingType",
     *   output="Ilios\CoreBundle\Entity\Offering",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="\d+",
     *         "description"="Offering identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated Offering.",
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
        $answer['offering'] =
            $this->getOfferingHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a Offering.
     *
     * @ApiDoc(
     *   section = "Offering",
     *   description = "Delete a Offering entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "Offering identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted Offering.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal OfferingInterface $offering
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $offering = $this->getOr404($id);

        try {
            $this->getOfferingHandler()
                ->deleteOffering($offering);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return OfferingInterface $offering
     */
    protected function getOr404($id)
    {
        $offering = $this->getOfferingHandler()
            ->findOfferingBy(['id' => $id]);
        if (!$offering) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $offering;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('offering');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return OfferingHandler
     */
    protected function getOfferingHandler()
    {
        return $this->container->get('ilioscore.offering.handler');
    }
}
