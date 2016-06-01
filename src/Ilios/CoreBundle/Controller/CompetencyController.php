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
use Ilios\CoreBundle\Entity\CompetencyInterface;

/**
 * Class CompetencyController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("Competencies")
 */
class CompetencyController extends FOSRestController
{
    /**
     * Get a Competency
     *
     * @ApiDoc(
     *   section = "Competency",
     *   description = "Get a Competency.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="Competency identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\Competency",
     *   statusCodes={
     *     200 = "Competency.",
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
        $competency = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $competency)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['competencies'][] = $competency;

        return $answer;
    }

    /**
     * Get all Competency.
     *
     * @ApiDoc(
     *   section = "Competency",
     *   description = "Get all Competency.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\Competency",
     *   statusCodes = {
     *     200 = "List of all Competency",
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

        $manager = $this->container->get('ilioscore.competency.manager');
        $result = $manager->findBy($criteria, $orderBy, $limit, $offset);

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['competencies'] =
            $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a Competency.
     *
     * @ApiDoc(
     *   section = "Competency",
     *   description = "Create a Competency.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CompetencyType",
     *   output="Ilios\CoreBundle\Entity\Competency",
     *   statusCodes={
     *     201 = "Created Competency.",
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
            $handler = $this->container->get('ilioscore.competency.handler');
            $competency = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $competency)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager = $this->container->get('ilioscore.competency.manager');
            $manager->update($competency, true, false);

            $answer['competencies'] = [$competency];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a Competency.
     *
     * @ApiDoc(
     *   section = "Competency",
     *   description = "Update a Competency entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CompetencyType",
     *   output="Ilios\CoreBundle\Entity\Competency",
     *   statusCodes={
     *     200 = "Updated Competency.",
     *     201 = "Created Competency.",
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
            $manager = $this->container->get('ilioscore.competency.manager');
            $competency = $manager->findOneBy(['id'=> $id]);
            if ($competency) {
                $code = Codes::HTTP_OK;
            } else {
                $competency = $manager->create();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->container->get('ilioscore.competency.handler');
            $competency = $handler->put($competency, $this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $competency)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager->update($competency, true, true);

            $answer['competency'] = $competency;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a Competency.
     *
     * @ApiDoc(
     *   section = "Competency",
     *   description = "Delete a Competency entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "Competency identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted Competency.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal CompetencyInterface $competency
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $competency = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $competency)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $manager = $this->container->get('ilioscore.competency.manager');
            $manager->delete($competency);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return CompetencyInterface $competency
     */
    protected function getOr404($id)
    {
        $manager = $this->container->get('ilioscore.competency.manager');
        $competency = $manager->findOneBy(['id' => $id]);
        if (!$competency) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $competency;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('competency')) {
            return $request->request->get('competency');
        }

        return $request->request->all();
    }
}
