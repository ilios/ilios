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
use Ilios\CoreBundle\Handler\SessionTypeHandler;
use Ilios\CoreBundle\Entity\SessionTypeInterface;

/**
 * Class SessionTypeController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("SessionTypes")
 */
class SessionTypeController extends FOSRestController
{
    /**
     * Get a SessionType
     *
     * @ApiDoc(
     *   section = "SessionType",
     *   description = "Get a SessionType.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="SessionType identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\SessionType",
     *   statusCodes={
     *     200 = "SessionType.",
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
        $answer['sessionType'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all SessionType.
     *
     * @ApiDoc(
     *   section = "SessionType",
     *   description = "Get all SessionType.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\SessionType",
     *   statusCodes = {
     *     200 = "List of all SessionType",
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

        $result = $this->getSessionTypeHandler()
            ->findSessionTypesBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        //If there are no matches return an empty array
        $answer['sessionTypes'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a SessionType.
     *
     * @ApiDoc(
     *   section = "SessionType",
     *   description = "Create a SessionType.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\SessionTypeType",
     *   output="Ilios\CoreBundle\Entity\SessionType",
     *   statusCodes={
     *     201 = "Created SessionType.",
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
            $sessiontype = $this->getSessionTypeHandler()
                ->post($this->getPostData($request));

            $response = new Response();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set(
                'Location',
                $this->generateUrl(
                    'get_sessiontypes',
                    ['id' => $sessiontype->getId()],
                    true
                )
            );

            return $response;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a SessionType.
     *
     * @ApiDoc(
     *   section = "SessionType",
     *   description = "Update a SessionType entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\SessionTypeType",
     *   output="Ilios\CoreBundle\Entity\SessionType",
     *   statusCodes={
     *     200 = "Updated SessionType.",
     *     201 = "Created SessionType.",
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
            $sessionType = $this->getSessionTypeHandler()
                ->findSessionTypeBy(['id'=> $id]);
            if ($sessionType) {
                $code = Codes::HTTP_OK;
            } else {
                $sessionType = $this->getSessionTypeHandler()->createSessionType();
                $code = Codes::HTTP_CREATED;
            }

            $answer['sessionType'] =
                $this->getSessionTypeHandler()->put(
                    $sessionType,
                    $this->getPostData($request)
                );
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a SessionType.
     *
     * @ApiDoc(
     *   section = "SessionType",
     *   description = "Partial Update to a SessionType.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\SessionTypeType",
     *   output="Ilios\CoreBundle\Entity\SessionType",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="\d+",
     *         "description"="SessionType identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated SessionType.",
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
        $answer['sessionType'] =
            $this->getSessionTypeHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a SessionType.
     *
     * @ApiDoc(
     *   section = "SessionType",
     *   description = "Delete a SessionType entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "SessionType identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted SessionType.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal SessionTypeInterface $sessionType
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $sessionType = $this->getOr404($id);

        try {
            $this->getSessionTypeHandler()->deleteSessionType($sessionType);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return SessionTypeInterface $sessionType
     */
    protected function getOr404($id)
    {
        $sessionType = $this->getSessionTypeHandler()
            ->findSessionTypeBy(['id' => $id]);
        if (!$sessionType) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $sessionType;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('sessionType');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return SessionTypeHandler
     */
    protected function getSessionTypeHandler()
    {
        return $this->container->get('ilioscore.sessiontype.handler');
    }
}
