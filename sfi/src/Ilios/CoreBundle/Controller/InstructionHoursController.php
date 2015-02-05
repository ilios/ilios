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
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Handler\InstructionHoursHandler;
use Ilios\CoreBundle\Entity\InstructionHoursInterface;

/**
 * InstructionHours controller.
 * @package Ilios\CoreBundle\Controller\;
 * @RouteResource("InstructionHours")
 */
class InstructionHoursController extends FOSRestController
{

    /**
     * Get a InstructionHours
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
        $answer['instructionHours'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all InstructionHours.
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

        $answer['instructionHours'] =
            $this->getInstructionHoursHandler()->findInstructionHoursBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        if ($answer['instructionHours']) {
            return $answer;
        }

        return new ArrayCollection([]);
    }

    /**
     * Create a InstructionHours.
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
            $new  =  $this->getInstructionHoursHandler()->post($request->request->all());
            $answer['instructionHours'] = $new;

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a InstructionHours.
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
            if ($instructionHours = $this->getInstructionHoursHandler()->findInstructionHoursBy(['id'=> $id])) {
                $answer['instructionHours']= $this->getInstructionHoursHandler()->put($instructionHours, $request->request->all());
                $code = Codes::HTTP_OK;
            } else {
                $answer['instructionHours'] = $this->getInstructionHoursHandler()->post($request->request->all());
                $code = Codes::HTTP_CREATED;
            }
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a InstructionHours.
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
        $answer['instructionHours'] = $this->getInstructionHoursHandler()->patch($this->getOr404($id), $request->request->all());

        return $answer;
    }

    /**
     * Delete a InstructionHours.
     *
     * @View(statusCode=204)
     *
     * @param Request $request
     * @param $id
     * @internal InstructionHoursInterface $instructionHours
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $instructionHours = $this->getOr404($id);
        try {
            $this->getInstructionHoursHandler()->deleteInstructionHours($instructionHours);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return InstructionHoursInterface $entity
     */
    protected function getOr404($id)
    {
        if (!($entity = $this->getInstructionHoursHandler()->findInstructionHoursBy(['id' => $id]))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $entity;
    }

    /**
     * @return InstructionHoursHandler
     */
    protected function getInstructionHoursHandler()
    {
        return $this->container->get('ilioscore.instructionhours.handler');
    }
}
