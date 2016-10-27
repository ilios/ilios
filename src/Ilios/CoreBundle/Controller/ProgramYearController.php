<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use Ilios\CoreBundle\Entity\CohortInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Entity\ProgramYearInterface;

/**
 * Class ProgramYearController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("ProgramYears")
 */
class ProgramYearController extends FOSRestController
{
    /**
     * Get a ProgramYear
     *
     * @ApiDoc(
     *   section = "ProgramYear",
     *   description = "Get a ProgramYear.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="ProgramYear identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\ProgramYear",
     *   statusCodes={
     *     200 = "ProgramYear.",
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
        $manager = $this->container->get('ilioscore.programyear.manager');
        $programYear = $manager->findDTOBy(['id' => $id]);

        if (!$programYear) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $programYear)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['programYears'][] = $programYear;

        return $answer;
    }

    /**
     * Get all ProgramYear.
     *
     * @ApiDoc(
     *   section = "ProgramYear",
     *   description = "Get all ProgramYear.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\ProgramYear",
     *   statusCodes = {
     *     200 = "List of all ProgramYear",
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

        $manager = $this->container->get('ilioscore.programyear.manager');
        $result = $manager->findDTOsBy($criteria, $orderBy, $limit, $offset);

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['programYears'] = $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a ProgramYear.
     *
     * @ApiDoc(
     *   section = "ProgramYear",
     *   description = "Create a ProgramYear.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\ProgramYearType",
     *   output="Ilios\CoreBundle\Entity\ProgramYear",
     *   statusCodes={
     *     201 = "Created ProgramYear.",
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
            $handler = $this->container->get('ilioscore.programyear.handler');
            /* @var ProgramYearInterface $programYear */
            $programYear = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $programYear)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $programYearManager = $this->container->get('ilioscore.programyear.manager');
            $this->createCohort($programYear);
            $programYearManager->update($programYear, true, false);

            $answer['programYears'] = [$programYear];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a ProgramYear.
     *
     * @ApiDoc(
     *   section = "ProgramYear",
     *   description = "Update a ProgramYear entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\ProgramYearType",
     *   output="Ilios\CoreBundle\Entity\ProgramYear",
     *   statusCodes={
     *     200 = "Updated ProgramYear.",
     *     201 = "Created ProgramYear.",
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
            $manager = $this->container->get('ilioscore.programyear.manager');

            /** @var ProgramYearInterface $course */
            $programYear = $manager->findOneBy(['id'=> $id]);
            $authChecker = $this->get('security.authorization_checker');
            $postData = $this->getPostData($request);

            if ($programYear) {
                $code = Codes::HTTP_OK;
                if ($programYear->isLocked() && !$postData['locked']) {
                    //check if the programYear can be unlocked and unlock it
                    if ($authChecker->isGranted('unlock', $programYear)) {
                        $programYear->setLocked(false);
                    }
                }
            } else {
                $programYear = $manager->create();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->container->get('ilioscore.programyear.handler');
            $programYear = $handler->put($programYear, $this->getPostData($request));

            if (! $authChecker->isGranted('edit', $programYear)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            if (empty($programYear->getCohort())) {
                $this->createCohort($programYear);
            }
            $manager->update($programYear, true, true);

            $answer['programYear'] = $programYear;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a ProgramYear.
     *
     * @ApiDoc(
     *   section = "ProgramYear",
     *   description = "Delete a ProgramYear entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "ProgramYear identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted ProgramYear.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal ProgramYearInterface $programYear
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $programYear = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted(['modify', 'delete'], $programYear)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $manager = $this->container->get('ilioscore.programyear.manager');
            $manager->delete($programYear);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return ProgramYearInterface $programYear
     */
    protected function getOr404($id)
    {
        $manager = $this->container->get('ilioscore.programyear.manager');
        $programYear = $manager->findOneBy(['id' => $id]);
        if (!$programYear) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $programYear;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('programYear')) {
            return $request->request->get('programYear');
        }

        return $request->request->all();
    }

    /**
     * Creates a new cohort for the new program year.
     * @param ProgramYearInterface $programYear
     */
    protected function createCohort(ProgramYearInterface $programYear)
    {
        $cohortManager = $this->container->get('ilioscore.cohort.manager');
        $program = $programYear->getProgram();
        $graduationYear = $programYear->getStartYear() + $program->getDuration();
        /* @var CohortInterface $cohort */
        $cohort = $cohortManager->create();
        $cohort->setTitle("Class of ${graduationYear}");
        $cohort->setProgramYear($programYear);
        $programYear->setCohort($cohort);
        $cohortManager->update($cohort, false, false);
    }
}
