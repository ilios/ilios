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
use Ilios\CoreBundle\Handler\ObjectiveHandler;
use Ilios\CoreBundle\Entity\ObjectiveInterface;

/**
 * Class ObjectiveController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("Objectives")
 */
class ObjectiveController extends FOSRestController
{
    /**
     * Get a Objective
     *
     * @ApiDoc(
     *   section = "Objective",
     *   description = "Get a Objective.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="Objective identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\Objective",
     *   statusCodes={
     *     200 = "Objective.",
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
        $answer['objective'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all Objective.
     *
     * @ApiDoc(
     *   section = "Objective",
     *   description = "Get all Objective.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\Objective",
     *   statusCodes = {
     *     200 = "List of all Objective",
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

        $result = $this->getObjectiveHandler()
            ->findObjectivesBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        //If there are no matches return an empty array
        $answer['objectives'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a Objective.
     *
     * @ApiDoc(
     *   section = "Objective",
     *   description = "Create a Objective.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\ObjectiveType",
     *   output="Ilios\CoreBundle\Entity\Objective",
     *   statusCodes={
     *     201 = "Created Objective.",
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
            $objective = $this->getObjectiveHandler()
                ->post($this->getPostData($request));

            $response = new Response();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set(
                'Location',
                $this->generateUrl(
                    'get_objectives',
                    ['id' => $objective->getId()],
                    true
                )
            );

            return $response;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a Objective.
     *
     * @ApiDoc(
     *   section = "Objective",
     *   description = "Update a Objective entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\ObjectiveType",
     *   output="Ilios\CoreBundle\Entity\Objective",
     *   statusCodes={
     *     200 = "Updated Objective.",
     *     201 = "Created Objective.",
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
            $objective = $this->getObjectiveHandler()
                ->findObjectiveBy(['id'=> $id]);
            if ($objective) {
                $code = Codes::HTTP_OK;
            } else {
                $objective = $this->getObjectiveHandler()->createObjective();
                $code = Codes::HTTP_CREATED;
            }

            $answer['objective'] =
                $this->getObjectiveHandler()->put(
                    $objective,
                    $this->getPostData($request)
                );
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a Objective.
     *
     * @ApiDoc(
     *   section = "Objective",
     *   description = "Partial Update to a Objective.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\ObjectiveType",
     *   output="Ilios\CoreBundle\Entity\Objective",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="\d+",
     *         "description"="Objective identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated Objective.",
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
        $answer['objective'] =
            $this->getObjectiveHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a Objective.
     *
     * @ApiDoc(
     *   section = "Objective",
     *   description = "Delete a Objective entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "Objective identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted Objective.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal ObjectiveInterface $objective
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $objective = $this->getOr404($id);

        try {
            $this->getObjectiveHandler()->deleteObjective($objective);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return ObjectiveInterface $objective
     */
    protected function getOr404($id)
    {
        $objective = $this->getObjectiveHandler()
            ->findObjectiveBy(['id' => $id]);
        if (!$objective) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $objective;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('objective');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return ObjectiveHandler
     */
    protected function getObjectiveHandler()
    {
        return $this->container->get('ilioscore.objective.handler');
    }
}
