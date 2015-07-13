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
use Ilios\CoreBundle\Handler\IlmSessionHandler;
use Ilios\CoreBundle\Entity\IlmSessionInterface;

/**
 * Class IlmSessionController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("IlmSessions")
 */
class IlmSessionController extends FOSRestController
{
    /**
     * Get a IlmSession
     *
     * @ApiDoc(
     *   section = "IlmSession",
     *   description = "Get a IlmSession.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
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
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return Response
     */
    public function getAction($id)
    {
        $answer['ilmSessions'][] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all IlmSession.
     *
     * @ApiDoc(
     *   section = "IlmSession",
     *   description = "Get all IlmSession.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\IlmSession",
     *   statusCodes = {
     *     200 = "List of all IlmSessions",
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
     * Create a IlmSession.
     *
     * @ApiDoc(
     *   section = "IlmSession",
     *   description = "Create a IlmSession.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\IlmSessionFacetType",
     *   output="Ilios\CoreBundle\Entity\IlmSession",
     *   statusCodes={
     *     201 = "Created IlmSession.",
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
            $new  =  $this->getIlmSessionFacetHandler()
                ->post($this->getPostData($request));
            $answer['ilmSessions'] = [$new];

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a IlmSession.
     *
     * @ApiDoc(
     *   section = "IlmSession",
     *   description = "Update a IlmSession entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\IlmSessionFacetType",
     *   output="Ilios\CoreBundle\Entity\IlmSession",
     *   statusCodes={
     *     200 = "Updated IlmSession.",
     *     201 = "Created IlmSession.",
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
            $ilmSessionFacet = $this->getIlmSessionFacetHandler()
                ->findIlmSessionFacetBy(['id'=> $id]);
            if ($ilmSessionFacet) {
                $code = Codes::HTTP_OK;
            } else {
                $ilmSessionFacet = $this->getIlmSessionFacetHandler()
                    ->createIlmSessionFacet();
                $code = Codes::HTTP_CREATED;
            }

            $answer['ilmSession'] =
                $this->getIlmSessionFacetHandler()->put(
                    $ilmSessionFacet,
                    $this->getPostData($request)
                );
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a IlmSession.
     *
     * @ApiDoc(
     *   section = "IlmSession",
     *   description = "Partial Update to a IlmSession.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\IlmSessionFacetType",
     *   output="Ilios\CoreBundle\Entity\IlmSession",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="\d+",
     *         "description"="IlmSession identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated IlmSession.",
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
        $answer['ilmSessions'] =
            $this->getIlmSessionFacetHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a IlmSession.
     *
     * @ApiDoc(
     *   section = "IlmSession",
     *   description = "Delete a IlmSession entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "IlmSession identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted IlmSession.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal IlmSessionInterface $ilmSessionFacet
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
     * @return IlmSessionInterface $ilmSessionFacet
     */
    protected function getOr404($id)
    {
        $ilmSessionFacet = $this->getIlmSessionFacetHandler()
            ->findIlmSessionFacetBy(['id' => $id]);
        if (!$ilmSessionFacet) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $ilmSessionFacet;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('ilmSession');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return IlmSessionHandler
     */
    protected function getIlmSessionFacetHandler()
    {
        return $this->container->get('ilioscore.ilmsessionfacet.handler');
    }
}
