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
use Ilios\CoreBundle\Handler\LearningMaterialStatusHandler;
use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;

/**
 * LearningMaterialStatus controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("LearningMaterialStatus")
 */
class LearningMaterialStatusController extends FOSRestController
{
    
    /**
     * Get a LearningMaterialStatus
     *
     * @ApiDoc(
     *   description = "Get a LearningMaterialStatus.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="",
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
     * @View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $id
     *
     * @return Response
     */
    public function getAction(Request $request, $id)
    {
        $answer['learningMaterialStatus'] = $this->getOr404($id);

        return $answer;
    }
    /**
     * Get all LearningMaterialStatus.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all LearningMaterialStatus.",
     *   output="Ilios\CoreBundle\Entity\LearningMaterialStatus",
     *   statusCodes = {
     *     200 = "List of all LearningMaterialStatus",
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
     *   resource = true,
     *   description = "Create a LearningMaterialStatus.",
     *   input="Ilios\CoreBundle\Form\LearningMaterialStatusType",
     *   output="Ilios\CoreBundle\Entity\LearningMaterialStatus",
     *   statusCodes={
     *     201 = "Created LearningMaterialStatus.",
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
            $new  =  $this->getLearningMaterialStatusHandler()->post($this->getPostData($request));
            $answer['learningMaterialStatus'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a LearningMaterialStatus.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a LearningMaterialStatus entity.",
     *   input="Ilios\CoreBundle\Form\LearningMaterialStatusType",
     *   output="Ilios\CoreBundle\Entity\LearningMaterialStatus",
     *   statusCodes={
     *     200 = "Updated LearningMaterialStatus.",
     *     201 = "Created LearningMaterialStatus.",
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
            $learningMaterialStatus = $this->getLearningMaterialStatusHandler()
                ->findLearningMaterialStatusBy(['id'=> $id]);
            if ($learningMaterialStatus) {
                $answer['learningMaterialStatus'] =
                    $this->getLearningMaterialStatusHandler()->put(
                        $learningMaterialStatus,
                        $this->getPostData($request)
                    );
                $code = Codes::HTTP_OK;
            } else {
                $answer['learningMaterialStatus'] =
                    $this->getLearningMaterialStatusHandler()->post($this->getPostData($request));
                $code = Codes::HTTP_CREATED;
            }
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
     *   resource = true,
     *   description = "Partial Update to a LearningMaterialStatus.",
     *   input="Ilios\CoreBundle\Form\LearningMaterialStatusType",
     *   output="Ilios\CoreBundle\Entity\LearningMaterialStatus",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="",
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
     *   description = "Delete a LearningMaterialStatus entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "",
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
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal LearningMaterialStatusInterface $learningMaterialStatus
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $learningMaterialStatus = $this->getOr404($id);
        try {
            $this->getLearningMaterialStatusHandler()
                ->deleteLearningMaterialStatus($learningMaterialStatus);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return LearningMaterialStatusInterface $entity
     */
    protected function getOr404($id)
    {
        $entity = $this->getLearningMaterialStatusHandler()
            ->findLearningMaterialStatusBy(['id' => $id]);
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
        return $request->request->get('learningMaterialStatus', array());
    }
    /**
     * @return LearningMaterialStatusHandler
     */
    protected function getLearningMaterialStatusHandler()
    {
        return $this->container->get('ilioscore.learningmaterialstatus.handler');
    }
}
