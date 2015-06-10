<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Handler\IlmSessionFacetHandler;
use Ilios\CoreBundle\Entity\IlmSessionFacetInterface;

/**
 * IlmSessionFacet controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("IlmSession")
 */
class IlmSessionController extends FOSRestController
{

    /**
     * Get a IlmSessionFacet
     *
     * @ApiDoc(
     *   description = "Get a IlmSession.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="",
     *        "description"="IlmSession identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\IlmSession",
     *   statusCodes={
     *     200 = "IlmSession.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @View(serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return Response
     */
    public function getAction($id)
    {
        $answer['ilmSession'] = $this->getOr404($id);

        return $answer;
    }
    /**
     * Get all IlmSessionFacet.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all IlmSessionFacet.",
     *   output="Ilios\CoreBundle\Entity\IlmSessionFacet",
     *   statusCodes = {
     *     200 = "List of all IlmSessionFacet",
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

        $criteria = array_map(function ($item) {
            $item = $item == 'null'?null:$item;
            $item = $item == 'false'?false:$item;
            $item = $item == 'true'?true:$item;
            return $item;
        }, $criteria);

        $result = $this->getIlmSessionFacetHandler()
            ->findIlmSessionFacetsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );
        //If there are no matches return an empty array
        $answer['ilmSessions'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a IlmSessionFacet.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a IlmSessionFacet.",
     *   input="Ilios\CoreBundle\Form\Type\IlmSessionFacetType",
     *   output="Ilios\CoreBundle\Entity\IlmSessionFacet",
     *   statusCodes={
     *     201 = "Created IlmSessionFacet.",
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
            $new  =  $this->getIlmSessionFacetHandler()->post($this->getPostData($request));
            $answer['ilmSession'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a IlmSessionFacet.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a IlmSessionFacet entity.",
     *   input="Ilios\CoreBundle\Form\Type\IlmSessionFacetType",
     *   output="Ilios\CoreBundle\Entity\IlmSessionFacet",
     *   statusCodes={
     *     200 = "Updated IlmSessionFacet.",
     *     201 = "Created IlmSessionFacet.",
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
            $ilmSessionFacet = $this->getIlmSessionFacetHandler()
                ->findIlmSessionFacetBy(['id'=> $id]);
            if ($ilmSessionFacet) {
                $answer['ilmSession'] =
                    $this->getIlmSessionFacetHandler()->put(
                        $ilmSessionFacet,
                        $this->getPostData($request)
                    );
                $code = Codes::HTTP_OK;
            } else {
                $answer['ilmSession'] =
                    $this->getIlmSessionFacetHandler()->post($this->getPostData($request));
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a IlmSessionFacet.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a IlmSessionFacet.",
     *   input="Ilios\CoreBundle\Form\Type\IlmSessionFacetType",
     *   output="Ilios\CoreBundle\Entity\IlmSessionFacet",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="",
     *         "description"="IlmSessionFacet identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated IlmSessionFacet.",
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
        $answer['ilmSession'] =
            $this->getIlmSessionFacetHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a IlmSessionFacet.
     *
     * @ApiDoc(
     *   description = "Delete a IlmSessionFacet entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "",
     *         "description" = "IlmSessionFacet identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted IlmSessionFacet.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param $id
     * @internal IlmSessionFacetInterface $ilmSessionFacet
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $ilmSessionFacet = $this->getOr404($id);
        try {
            $this->getIlmSessionFacetHandler()
                ->deleteIlmSessionFacet($ilmSessionFacet);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return IlmSessionFacetInterface $entity
     */
    protected function getOr404($id)
    {
        $entity = $this->getIlmSessionFacetHandler()
            ->findIlmSessionFacetBy(['id' => $id]);
        if (!$entity) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $entity;
    }
   /**
    * Parse the request for the form data
    *
    * @param Request $request
    * @return array
     */
    protected function getPostData(Request $request)
    {
        return $request->request->get('ilmSession', array());
    }
    /**
     * @return IlmSessionFacetHandler
     */
    protected function getIlmSessionFacetHandler()
    {
        return $this->container->get('ilioscore.ilmsessionfacet.handler');
    }
}
