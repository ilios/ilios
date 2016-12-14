<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ilios\CoreBundle\Exception\InvalidFormException;
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
        $manager = $this->container->get('ilioscore.assessmentoption.manager');

        $assessmentOption = $manager->findDTOBy(['id' => $id]);
        if (!$assessmentOption) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $assessmentOption)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['assessmentOptions'][] = $assessmentOption;

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

        $manager = $this->container->get('ilioscore.assessmentoption.manager');
        $result = $manager->findDTOsBy($criteria, $orderBy, $limit, $offset);

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['assessmentOptions'] =
            $result ? array_values($result) : [];

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
            $handler = $this->container->get('ilioscore.assessmentoption.handler');
            $assessmentOption = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $assessmentOption)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager = $this->container->get('ilioscore.assessmentoption.manager');
            $manager->update($assessmentOption, true, false);

            $answer['assessmentOptions'] = [$assessmentOption];

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
            $manager = $this->container->get('ilioscore.assessmentoption.manager');
            $assessmentOption = $manager->findOneBy(['id'=> $id]);
            if ($assessmentOption) {
                $code = Codes::HTTP_OK;
            } else {
                $assessmentOption = $manager->create();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->container->get('ilioscore.assessmentoption.handler');
            $assessmentOption = $handler->put($assessmentOption, $this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $assessmentOption)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager->update($assessmentOption, true, true);

            $answer['assessmentOption'] = $assessmentOption;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
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

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $assessmentOption)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $manager = $this->container->get('ilioscore.assessmentoption.manager');
            $manager->delete($assessmentOption);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
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
        $manager = $this->container->get('ilioscore.assessmentoption.manager');
        $assessmentOption = $manager->findOneBy(['id' => $id]);
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
        if ($request->request->has('assessmentOption')) {
            return $request->request->get('assessmentOption');
        }

        return $request->request->all();
    }
}
