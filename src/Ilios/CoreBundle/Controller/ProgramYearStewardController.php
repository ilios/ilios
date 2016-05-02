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
use Ilios\CoreBundle\Handler\ProgramYearStewardHandler;
use Ilios\CoreBundle\Entity\ProgramYearStewardInterface;

/**
 * Class ProgramYearStewardController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("ProgramYearStewards")
 */
class ProgramYearStewardController extends FOSRestController
{
    /**
     * Get a ProgramYearSteward
     *
     * @ApiDoc(
     *   section = "ProgramYearSteward",
     *   description = "Get a ProgramYearSteward.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="ProgramYearSteward identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\ProgramYearSteward",
     *   statusCodes={
     *     200 = "ProgramYearSteward.",
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
        $programYearSteward = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $programYearSteward)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['programYearStewards'][] = $programYearSteward;

        return $answer;
    }

    /**
     * Get all ProgramYearSteward.
     *
     * @ApiDoc(
     *   section = "ProgramYearSteward",
     *   description = "Get all ProgramYearSteward.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\ProgramYearSteward",
     *   statusCodes = {
     *     200 = "List of all ProgramYearSteward",
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

        $result = $this->getProgramYearStewardHandler()
            ->findProgramYearStewardsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['programYearStewards'] =
            $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a ProgramYearSteward.
     *
     * @ApiDoc(
     *   section = "ProgramYearSteward",
     *   description = "Create a ProgramYearSteward.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\ProgramYearStewardType",
     *   output="Ilios\CoreBundle\Entity\ProgramYearSteward",
     *   statusCodes={
     *     201 = "Created ProgramYearSteward.",
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
            $handler = $this->getProgramYearStewardHandler();

            $programYearSteward = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $programYearSteward)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getProgramYearStewardHandler()->updateProgramYearSteward($programYearSteward, true, false);

            $answer['programYearStewards'] = [$programYearSteward];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a ProgramYearSteward.
     *
     * @ApiDoc(
     *   section = "ProgramYearSteward",
     *   description = "Update a ProgramYearSteward entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\ProgramYearStewardType",
     *   output="Ilios\CoreBundle\Entity\ProgramYearSteward",
     *   statusCodes={
     *     200 = "Updated ProgramYearSteward.",
     *     201 = "Created ProgramYearSteward.",
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
            $programYearSteward = $this->getProgramYearStewardHandler()
                ->findProgramYearStewardBy(['id'=> $id]);
            if ($programYearSteward) {
                $code = Codes::HTTP_OK;
            } else {
                $programYearSteward = $this->getProgramYearStewardHandler()
                    ->createProgramYearSteward();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->getProgramYearStewardHandler();

            $programYearSteward = $handler->put(
                $programYearSteward,
                $this->getPostData($request)
            );

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $programYearSteward)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $this->getProgramYearStewardHandler()->updateProgramYearSteward($programYearSteward, true, true);

            $answer['programYearSteward'] = $programYearSteward;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a ProgramYearSteward.
     *
     * @ApiDoc(
     *   section = "ProgramYearSteward",
     *   description = "Delete a ProgramYearSteward entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "ProgramYearSteward identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted ProgramYearSteward.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal ProgramYearStewardInterface $programYearSteward
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $programYearSteward = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $programYearSteward)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $this->getProgramYearStewardHandler()
                ->deleteProgramYearSteward($programYearSteward);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return ProgramYearStewardInterface $programYearSteward
     */
    protected function getOr404($id)
    {
        $programYearSteward = $this->getProgramYearStewardHandler()
            ->findProgramYearStewardBy(['id' => $id]);
        if (!$programYearSteward) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $programYearSteward;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('programYearSteward')) {
            return $request->request->get('programYearSteward');
        }

        return $request->request->all();
    }

    /**
     * @return ProgramYearStewardHandler
     */
    protected function getProgramYearStewardHandler()
    {
        return $this->container->get('ilioscore.programyearsteward.handler');
    }
}
