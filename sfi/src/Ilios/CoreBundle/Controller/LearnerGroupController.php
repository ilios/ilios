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
use Ilios\CoreBundle\Handler\LearnerGroupHandler;
use Ilios\CoreBundle\Entity\LearnerGroupInterface;

/**
 * LearnerGroup controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("LearnerGroup")
 */
class LearnerGroupController extends FOSRestController
{

    /**
     * Get a LearnerGroup
     *
     * @ApiDoc(
     *   description = "Get a LearnerGroup.",
     *   resource = true,
     *   requirements={
     *     {"name"="id", "dataType"="integer", "requirement"="", "description"="LearnerGroup identifier."}
     *   },
     *   output="Ilios\CoreBundle\Entity\LearnerGroup",
     *   statusCodes={
     *     200 = "LearnerGroup.",
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
        $answer['learnerGroup'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all LearnerGroup.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get all LearnerGroup.",
     *   output="Ilios\CoreBundle\Entity\LearnerGroup",
     *   statusCodes = {
     *     200 = "List of all LearnerGroup",
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

        $answer['learnerGroup'] =
            $this->getLearnerGroupHandler()->findLearnerGroupsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['learnerGroup']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a LearnerGroup.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a LearnerGroup.",
     *   input="Ilios\CoreBundle\Form\LearnerGroupType",
     *   output="Ilios\CoreBundle\Entity\LearnerGroup",
     *   statusCodes={
     *     201 = "Created LearnerGroup.",
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
            $new  =  $this->getLearnerGroupHandler()->post($request->request->all());
            $answer['learnerGroup'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a LearnerGroup.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update a LearnerGroup entity.",
     *   input="Ilios\CoreBundle\Form\LearnerGroupType",
     *   output="Ilios\CoreBundle\Entity\LearnerGroup",
     *   statusCodes={
     *     200 = "Updated LearnerGroup.",
     *     201 = "Created LearnerGroup.",
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
            if ($learnerGroup = $this->getLearnerGroupHandler()->findLearnerGroupBy(['id'=> $id])) {
                $answer['learnerGroup']= $this->getLearnerGroupHandler()->put($learnerGroup, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['learnerGroup'] = $this->getLearnerGroupHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a LearnerGroup.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Partial Update to a LearnerGroup.",
     *   input="Ilios\CoreBundle\Form\LearnerGroupType",
     *   output="Ilios\CoreBundle\Entity\LearnerGroup",
     *   requirements={
     *     {"name"="id", "dataType"="integer", "requirement"="", "description"="LearnerGroup identifier."}
     *   },
     *   statusCodes={
     *     200 = "Updated LearnerGroup.",
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
        $answer['learnerGroup'] = $this->getLearnerGroupHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a LearnerGroup.
     *
     * @ApiDoc(
     *   description = "Delete a LearnerGroup entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "",
     *         "description" = "LearnerGroup identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted LearnerGroup.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal LearnerGroupInterface $learnerGroup
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $learnerGroup = $this->getOr404($id);
        try {
            $this->getLearnerGroupHandler()->deleteLearnerGroup($learnerGroup);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return LearnerGroupInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getLearnerGroupHandler()->findLearnerGroupBy(['id' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $entity;
    }

    /**
     * @return LearnerGroupHandler
     */
    protected function getLearnerGroupHandler()
    {
        return $this->container->get('ilioscore.learnergroup.handler');
    }
}
