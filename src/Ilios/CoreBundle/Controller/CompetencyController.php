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
use Ilios\CoreBundle\Handler\CompetencyHandler;
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
        $answer['competency'] = $this->getOr404($id);

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

        $result = $this->getCompetencyHandler()
            ->findCompetenciesBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        //If there are no matches return an empty array
        $answer['competencies'] =
            $result ? $result : new ArrayCollection([]);

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
            $competency = $this->getCompetencyHandler()
                ->post($this->getPostData($request));

            $response = new Response();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set(
                'Location',
                $this->generateUrl(
                    'get_competencies',
                    ['id' => $competency->getId()],
                    true
                )
            );

            return $response;
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
            $competency = $this->getCompetencyHandler()
                ->findCompetencyBy(['id'=> $id]);
            if ($competency) {
                $code = Codes::HTTP_OK;
            } else {
                $competency = $this->getCompetencyHandler()->createCompetency();
                $code = Codes::HTTP_CREATED;
            }

            $answer['competency'] =
                $this->getCompetencyHandler()->put(
                    $competency,
                    $this->getPostData($request)
                );
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a Competency.
     *
     * @ApiDoc(
     *   section = "Competency",
     *   description = "Partial Update to a Competency.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CompetencyType",
     *   output="Ilios\CoreBundle\Entity\Competency",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="\d+",
     *         "description"="Competency identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated Competency.",
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
        $answer['competency'] =
            $this->getCompetencyHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
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

        try {
            $this->getCompetencyHandler()->deleteCompetency($competency);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
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
        $competency = $this->getCompetencyHandler()
            ->findCompetencyBy(['id' => $id]);
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
        $data = $request->request->get('competency');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return CompetencyHandler
     */
    protected function getCompetencyHandler()
    {
        return $this->container->get('ilioscore.competency.handler');
    }
}
