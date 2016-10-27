<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Entity\CohortInterface;

/**
 * Class CohortController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("Cohorts")
 */
class CohortController extends FOSRestController
{
    /**
     * Get a Cohort
     *
     * @ApiDoc(
     *   section = "Cohort",
     *   description = "Get a Cohort.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="Cohort identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\Cohort",
     *   statusCodes={
     *     200 = "Cohort.",
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
        $manager = $this->container->get('ilioscore.cohort.manager');
        $cohort = $manager->findDTOBy(['id' => $id]);

        if (!$cohort) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $cohort)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['cohorts'][] = $cohort;

        return $answer;
    }

    /**
     * Get all Cohort.
     *
     * @ApiDoc(
     *   section = "Cohort",
     *   description = "Get all Cohort.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\Cohort",
     *   statusCodes = {
     *     200 = "List of all Cohort",
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

        $manager = $this->container->get('ilioscore.cohort.manager');
        $result = $manager->findDTOsBy($criteria, $orderBy, $limit, $offset);

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['cohorts'] =
            $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a Cohort.
     *
     * @ApiDoc(
     *   section = "Cohort",
     *   description = "Create a Cohort.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CohortType",
     *   statusCodes={
     *     410 = "Gone.",
     *   },
     *   deprecated = true
     * )
     *
     * @Rest\View(statusCode=201, serializerEnableMaxDepthChecks=true)
     *
     * @return Response
     */
    public function postAction()
    {
        $this->throwCreatingCohortNotSupportedException();
    }

    /**
     * Update a Cohort.
     *
     * @ApiDoc(
     *   section = "Cohort",
     *   description = "Update a Cohort entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CohortType",
     *   output="Ilios\CoreBundle\Entity\Cohort",
     *   statusCodes={
     *     200 = "Updated Cohort.",
     *     201 = "Created Cohort.",
     *     400 = "Bad Request.",
     *     404 = "Not Found.",
     *     410 = "Gone."
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
            $manager = $this->container->get('ilioscore.cohort.manager');
            $cohort = $manager->findOneBy(['id'=> $id]);
            if ($cohort) {
                $code = Codes::HTTP_OK;
            } else {
                $this->throwCreatingCohortNotSupportedException();
            }

            $handler = $this->container->get('ilioscore.cohort.handler');
            $cohort = $handler->put($cohort, $this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $cohort)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager->update($cohort, true, true);

            $answer['cohort'] = $cohort;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a Cohort.
     *
     * @ApiDoc(
     *   section = "Cohort",
     *   description = "Delete a Cohort entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "Cohort identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted Cohort.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal CohortInterface $cohort
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $cohort = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $cohort)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $manager = $this->container->get('ilioscore.cohort.manager');
            $manager->delete($cohort);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return CohortInterface $cohort
     */
    protected function getOr404($id)
    {
        $manager = $this->container->get('ilioscore.cohort.manager');
        $cohort = $manager->findOneBy(['id' => $id]);
        if (!$cohort) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $cohort;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('cohort')) {
            return $request->request->get('cohort');
        }

        return $request->request->all();
    }

    /**
     * @throws GoneHttpException
     */
    protected function throwCreatingCohortNotSupportedException()
    {
        throw new GoneHttpException('Explicitly creating cohorts is no longer supported.');
    }
}
