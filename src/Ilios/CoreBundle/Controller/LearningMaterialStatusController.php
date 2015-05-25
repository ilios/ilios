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
use Ilios\CoreBundle\Handler\LearningMaterialStatusHandler;
use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;

/**
 * Class LearningMaterialStatusController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("LearningMaterialStatuses")
 */
class LearningMaterialStatusController extends FOSRestController
{
    /**
     * Get a LearningMaterialStatus
     *
     * @ApiDoc(
     *   section = "LearningMaterialStatus",
     *   description = "Get a LearningMaterialStatus.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="LearningMaterialStatus identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\LearningMaterialStatus",
     *   statusCodes={
     *     200 = "LearningMaterialStatus.",
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
        $answer['learningMaterialStatus'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all LearningMaterialStatus.
     *
     * @ApiDoc(
     *   section = "LearningMaterialStatus",
     *   description = "Get all LearningMaterialStatus.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\LearningMaterialStatus",
     *   statusCodes = {
     *     200 = "List of all LearningMaterialStatus",
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

        $result = $this->getLearningMaterialStatusHandler()
            ->findLearningMaterialStatusesBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        //If there are no matches return an empty array
        $answer['learningMaterialStatuses'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a LearningMaterialStatus.
     *
     * @ApiDoc(
     *   section = "LearningMaterialStatus",
     *   description = "Create a LearningMaterialStatus.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\LearningMaterialStatusType",
     *   output="Ilios\CoreBundle\Entity\LearningMaterialStatus",
     *   statusCodes={
     *     201 = "Created LearningMaterialStatus.",
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
            $learningmaterialstatus = $this->getLearningMaterialStatusHandler()
                ->post($this->getPostData($request));

            $response = new Response();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set(
                'Location',
                $this->generateUrl(
                    'get_learningmaterialstatuses',
                    ['id' => $learningmaterialstatus->getId()],
                    true
                )
            );

            return $response;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a LearningMaterialStatus.
     *
     * @ApiDoc(
     *   section = "LearningMaterialStatus",
     *   description = "Update a LearningMaterialStatus entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\LearningMaterialStatusType",
     *   output="Ilios\CoreBundle\Entity\LearningMaterialStatus",
     *   statusCodes={
     *     200 = "Updated LearningMaterialStatus.",
     *     201 = "Created LearningMaterialStatus.",
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
            $learningMaterialStatus = $this->getLearningMaterialStatusHandler()
                ->findLearningMaterialStatusBy(['id'=> $id]);
            if ($learningMaterialStatus) {
                $code = Codes::HTTP_OK;
            } else {
                $learningMaterialStatus = $this->getLearningMaterialStatusHandler()->createLearningMaterialStatus();
                $code = Codes::HTTP_CREATED;
            }

            $answer['learningMaterialStatus'] =
                $this->getLearningMaterialStatusHandler()->put(
                    $learningMaterialStatus,
                    $this->getPostData($request)
                );
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a LearningMaterialStatus.
     *
     * @ApiDoc(
     *   section = "LearningMaterialStatus",
     *   description = "Partial Update to a LearningMaterialStatus.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\LearningMaterialStatusType",
     *   output="Ilios\CoreBundle\Entity\LearningMaterialStatus",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="\d+",
     *         "description"="LearningMaterialStatus identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated LearningMaterialStatus.",
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
        $answer['learningMaterialStatus'] =
            $this->getLearningMaterialStatusHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a LearningMaterialStatus.
     *
     * @ApiDoc(
     *   section = "LearningMaterialStatus",
     *   description = "Delete a LearningMaterialStatus entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "LearningMaterialStatus identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted LearningMaterialStatus.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal LearningMaterialStatusInterface $learningMaterialStatus
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $learningMaterialStatus = $this->getOr404($id);

        try {
            $this->getLearningMaterialStatusHandler()->deleteLearningMaterialStatus($learningMaterialStatus);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return LearningMaterialStatusInterface $learningMaterialStatus
     */
    protected function getOr404($id)
    {
        $learningMaterialStatus = $this->getLearningMaterialStatusHandler()
            ->findLearningMaterialStatusBy(['id' => $id]);
        if (!$learningMaterialStatus) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $learningMaterialStatus;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('learningMaterialStatus');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return LearningMaterialStatusHandler
     */
    protected function getLearningMaterialStatusHandler()
    {
        return $this->container->get('ilioscore.learningmaterialstatus.handler');
    }
}
