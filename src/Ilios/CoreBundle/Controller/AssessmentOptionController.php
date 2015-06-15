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
use Ilios\CoreBundle\Handler\AssessmentOptionHandler;
use Ilios\CoreBundle\Entity\AssessmentOptionInterface;

/**
 * Class AssessmentOptionController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("AssessmentOptions")
 */
class AssessmentOptionController extends FOSRestController
{
    /**
     * Get a AssessmentOption
     *
     * @ApiDoc(
     *   section = "AssessmentOption",
     *   description = "Get a AssessmentOption.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="AssessmentOption identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\AssessmentOption",
     *   statusCodes={
     *     200 = "AssessmentOption.",
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
        $answer['assessmentOptions'][] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all AssessmentOption.
     *
     * @ApiDoc(
     *   section = "AssessmentOption",
     *   description = "Get all AssessmentOption.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\AssessmentOption",
     *   statusCodes = {
     *     200 = "List of all AssessmentOption",
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

        $result = $this->getAssessmentOptionHandler()
            ->findAssessmentOptionsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        //If there are no matches return an empty array
        $answer['assessmentOptions'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a AssessmentOption.
     *
     * @ApiDoc(
     *   section = "AssessmentOption",
     *   description = "Create a AssessmentOption.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\AssessmentOptionType",
     *   output="Ilios\CoreBundle\Entity\AssessmentOption",
     *   statusCodes={
     *     201 = "Created AssessmentOption.",
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
            $new  =  $this->getAssessmentOptionHandler()
                ->post($this->getPostData($request));
            $answer['assessmentOptions'] = [$new];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a AssessmentOption.
     *
     * @ApiDoc(
     *   section = "AssessmentOption",
     *   description = "Update a AssessmentOption entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\AssessmentOptionType",
     *   output="Ilios\CoreBundle\Entity\AssessmentOption",
     *   statusCodes={
     *     200 = "Updated AssessmentOption.",
     *     201 = "Created AssessmentOption.",
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
            $assessmentOption = $this->getAssessmentOptionHandler()
                ->findAssessmentOptionBy(['id'=> $id]);
            if ($assessmentOption) {
                $code = Codes::HTTP_OK;
            } else {
                $assessmentOption = $this->getAssessmentOptionHandler()
                    ->createAssessmentOption();
                $code = Codes::HTTP_CREATED;
            }

            $answer['assessmentOption'] =
                $this->getAssessmentOptionHandler()->put(
                    $assessmentOption,
                    $this->getPostData($request)
                );
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a AssessmentOption.
     *
     * @ApiDoc(
     *   section = "AssessmentOption",
     *   description = "Partial Update to a AssessmentOption.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\AssessmentOptionType",
     *   output="Ilios\CoreBundle\Entity\AssessmentOption",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="\d+",
     *         "description"="AssessmentOption identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated AssessmentOption.",
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
        $answer['assessmentOption'] =
            $this->getAssessmentOptionHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a AssessmentOption.
     *
     * @ApiDoc(
     *   section = "AssessmentOption",
     *   description = "Delete a AssessmentOption entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "AssessmentOption identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted AssessmentOption.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal AssessmentOptionInterface $assessmentOption
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $assessmentOption = $this->getOr404($id);

        try {
            $this->getAssessmentOptionHandler()
                ->deleteAssessmentOption($assessmentOption);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return AssessmentOptionInterface $assessmentOption
     */
    protected function getOr404($id)
    {
        $assessmentOption = $this->getAssessmentOptionHandler()
            ->findAssessmentOptionBy(['id' => $id]);
        if (!$assessmentOption) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $assessmentOption;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('assessmentOption');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return AssessmentOptionHandler
     */
    protected function getAssessmentOptionHandler()
    {
        return $this->container->get('ilioscore.assessmentoption.handler');
    }
}
