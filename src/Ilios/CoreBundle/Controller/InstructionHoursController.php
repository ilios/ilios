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
use Ilios\CoreBundle\Handler\InstructionHoursHandler;
use Ilios\CoreBundle\Entity\InstructionHoursInterface;

/**
 * Class InstructionHoursController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("InstructionHours")
 */
class InstructionHoursController extends FOSRestController
{
    /**
     * Get a InstructionHours
     *
     * @ApiDoc(
     *   section = "InstructionHours",
     *   description = "Get a InstructionHours.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="InstructionHours identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\InstructionHours",
     *   statusCodes={
     *     200 = "InstructionHours.",
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
        $answer['instructionHours'][] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all InstructionHours.
     *
     * @ApiDoc(
     *   section = "InstructionHours",
     *   description = "Get all InstructionHours.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\InstructionHours",
     *   statusCodes = {
     *     200 = "List of all InstructionHours",
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

        $result = $this->getInstructionHoursHandler()
            ->findInstructionHoursBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        //If there are no matches return an empty array
        $answer['instructionHours'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a InstructionHours.
     *
     * @ApiDoc(
     *   section = "InstructionHours",
     *   description = "Create a InstructionHours.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\InstructionHoursType",
     *   output="Ilios\CoreBundle\Entity\InstructionHours",
     *   statusCodes={
     *     201 = "Created InstructionHours.",
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
            $new  =  $this->getInstructionHoursHandler()
                ->post($this->getPostData($request));
            $answer['instructionHours'] = [$new];

            return $answer;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a InstructionHours.
     *
     * @ApiDoc(
     *   section = "InstructionHours",
     *   description = "Update a InstructionHours entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\InstructionHoursType",
     *   output="Ilios\CoreBundle\Entity\InstructionHours",
     *   statusCodes={
     *     200 = "Updated InstructionHours.",
     *     201 = "Created InstructionHours.",
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
            $instructionHours = $this->getInstructionHoursHandler()
                ->findInstructionHoursBy(['id'=> $id]);
            if ($instructionHours) {
                $code = Codes::HTTP_OK;
            } else {
                $instructionHours = $this->getInstructionHoursHandler()
                    ->createInstructionHours();
                $code = Codes::HTTP_CREATED;
            }

            $answer['instructionHours'] =
                $this->getInstructionHoursHandler()->put(
                    $instructionHours,
                    $this->getPostData($request)
                );
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a InstructionHours.
     *
     * @ApiDoc(
     *   section = "InstructionHours",
     *   description = "Partial Update to a InstructionHours.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\InstructionHoursType",
     *   output="Ilios\CoreBundle\Entity\InstructionHours",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="\d+",
     *         "description"="InstructionHours identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated InstructionHours.",
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
        $answer['instructionHours'] =
            $this->getInstructionHoursHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a InstructionHours.
     *
     * @ApiDoc(
     *   section = "InstructionHours",
     *   description = "Delete a InstructionHours entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "InstructionHours identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted InstructionHours.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal InstructionHoursInterface $instructionHours
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $instructionHours = $this->getOr404($id);

        try {
            $this->getInstructionHoursHandler()
                ->deleteInstructionHours($instructionHours);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return InstructionHoursInterface $instructionHours
     */
    protected function getOr404($id)
    {
        $instructionHours = $this->getInstructionHoursHandler()
            ->findInstructionHoursBy(['id' => $id]);
        if (!$instructionHours) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $instructionHours;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('instructionHours');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return InstructionHoursHandler
     */
    protected function getInstructionHoursHandler()
    {
        return $this->container->get('ilioscore.instructionhours.handler');
    }
}
